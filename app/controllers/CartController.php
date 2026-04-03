<?php

/**
 * ============================================================
 * app/controllers/CartController.php
 * ============================================================
 * Handles shopping cart routes.
 * ============================================================
 */

class CartController extends Controller
{
    /**
     * Ensure old cart format [productId => qty] is migrated.
     */
    private function migrateLegacyCart(array $raw): array
    {
        $migrated = [];

        foreach ($raw as $key => $value) {
            if (is_array($value)) {
                $productId = (int) ($value['product_id'] ?? 0);
                $size      = isset($value['size']) && $value['size'] !== '' ? (string) $value['size'] : null;
                $qty       = max(1, (int) ($value['qty'] ?? 1));
                $composite = (string) $key;
            } else {
                $productId = (int) $key;
                $size      = null;
                $qty       = max(1, (int) $value);
                $composite = $productId . ':';
            }

            if ($productId <= 0) {
                continue;
            }

            $migrated[$composite] = [
                'product_id' => $productId,
                'size'       => $size,
                'qty'        => $qty,
            ];
        }

        return $migrated;
    }

    /**
     * @return array<int, array{key:string, product_id:int, qty:int, size:?string}>
     */
    private function normalizeCart(array $raw): array
    {
        $items = [];

        foreach ($raw as $key => $value) {
            if (is_array($value)) {
                $productId = (int) ($value['product_id'] ?? 0);
                $qty       = max(1, (int) ($value['qty'] ?? 1));
                $size      = isset($value['size']) && $value['size'] !== '' ? (string) $value['size'] : null;
                $composite = (string) $key;
            } else {
                // Backward compatibility for old format: [productId => qty]
                $productId = (int) $key;
                $qty       = max(1, (int) $value);
                $size      = null;
                $composite = $productId . ':';
            }

            if ($productId <= 0) {
                continue;
            }

            $items[] = [
                'key'        => $composite,
                'product_id' => $productId,
                'qty'        => $qty,
                'size'       => $size,
            ];
        }

        return $items;
    }

    public function index(): void
    {
        $raw          = Session::get('cart') ?? [];
        $raw          = $this->migrateLegacyCart($raw);
        Session::set('cart', $raw);
        $productModel = new Product();
        $items        = [];
        $subtotal     = 0;
        $cartRows     = $this->normalizeCart($raw);

        foreach ($cartRows as $row) {
            // Use findById for cart display (doesn't filter by is_active)
            // This ensures cart items show even if product was deactivated
            $product = $productModel->findById((int) $row['product_id']);
            if (!$product) {
                continue;
            }
            $lineTotal  = (float) $product->price * (int) $row['qty'];
            $subtotal  += $lineTotal;
            $items[]    = [
                'key'       => $row['key'],
                'product'   => $product,
                'qty'       => (int) $row['qty'],
                'size'      => $row['size'],
                'lineTotal' => $lineTotal,
            ];
        }

        $this->render('cart.index', [
            'pageTitle' => 'Your Cart — ' . APP_NAME,
            'items'     => $items,
            'subtotal'  => $subtotal,
        ]);
    }

    public function add(): void
    {
        $this->verifyCsrf();

        // Only treat as AJAX if X-Requested-With header is set OR Accept header wants JSON
        $isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
            (strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false &&
                strpos($_SERVER['HTTP_ACCEPT'] ?? '', 'text/html') === false);

        $productId = (int) ($_POST['product_id'] ?? 0);
        $quantity  = max(1, (int) ($_POST['quantity'] ?? 1));
        $size      = trim((string) ($_POST['size'] ?? ''));
        $size      = $size !== '' ? $size : null;

        if ($productId <= 0) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid product.']);
                return;
            }
            $this->flash('error', 'Invalid product.');
            $this->redirectBack(url('products'));
        }

        $sizeModel = new ProductSize();
        $availableSizes = $sizeModel->findAvailable($productId);
        if (!empty($availableSizes) && $size === null) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Please select a size.']);
                return;
            }
            $this->flash('error', 'Please select a size before adding this product to cart.');
            $this->redirectBack(url('product/' . $productId));
        }

        if ($size !== null && !$sizeModel->isAvailable($productId, $size, $quantity)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Selected size is unavailable.']);
                return;
            }
            $this->flash('error', 'Selected size is unavailable for that quantity.');
            $this->redirectBack(url('product/' . $productId));
        }

        $cart = Session::get('cart') ?? [];
        $key  = $productId . ':' . ($size ?? '');
        if (!isset($cart[$key])) {
            $cart[$key] = [
                'product_id' => $productId,
                'size'       => $size,
                'qty'        => 0,
            ];
        }
        $cart[$key]['qty'] = (int) $cart[$key]['qty'] + $quantity;
        Session::set('cart', $cart);

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Item added to cart.']);
            return;
        }

        $this->flash('success', 'Item added to cart.');
        $this->redirect(url('cart'));
    }

    public function update(): void
    {
        $this->verifyCsrf();

        $cartKey   = (string) ($_POST['cart_key'] ?? '');
        $quantity  = max(0, (int) ($_POST['quantity'] ?? 0));

        $cart = Session::get('cart') ?? [];
        if ($cartKey === '' || !isset($cart[$cartKey])) {
            $this->redirect(url('cart'));
        }

        if ($quantity <= 0) {
            unset($cart[$cartKey]);
        } else {
            $cart[$cartKey]['qty'] = $quantity;
        }
        Session::set('cart', $cart);

        $this->flash('success', 'Cart updated.');
        $this->redirect(url('cart'));
    }

    public function remove(): void
    {
        $this->verifyCsrf();

        $cartKey = (string) ($_POST['cart_key'] ?? '');
        $cart = Session::get('cart') ?? [];
        if ($cartKey !== '') {
            unset($cart[$cartKey]);
        }
        Session::set('cart', $cart);

        $this->flash('success', 'Item removed.');
        $this->redirect(url('cart'));
    }
}
