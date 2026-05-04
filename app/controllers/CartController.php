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
     * Ensure old cart format [productId => qty] is migrated to the current structure.
     */
    private function migrateLegacyCart(array $raw): array
    {
        $migrated = [];

        foreach ($raw as $key => $value) {
            if (is_array($value)) {
                $productId = (int) ($value['product_id'] ?? 0);
                $size      = isset($value['size'])    && $value['size']    !== '' ? (string) $value['size']    : null;
                $color     = isset($value['color'])   && $value['color']   !== '' ? (string) $value['color']   : null;
                $quality   = isset($value['quality']) && $value['quality'] !== '' ? (string) $value['quality'] : null;
                $qty       = max(1, (int) ($value['qty'] ?? 1));
                $composite = (string) $key;
            } else {
                $productId = (int) $key;
                $size      = null;
                $color     = null;
                $quality   = null;
                $qty       = max(1, (int) $value);
                $composite = $productId . ':::';
            }

            if ($productId <= 0) {
                continue;
            }

            $migrated[$composite] = [
                'product_id' => $productId,
                'size'       => $size,
                'color'      => $color,
                'quality'    => $quality,
                'qty'        => $qty,
            ];
        }

        return $migrated;
    }

    /**
     * @return array<int, array{key:string, product_id:int, qty:int, size:?string, color:?string, quality:?string}>
     */
    private function normalizeCart(array $raw): array
    {
        $items = [];

        foreach ($raw as $key => $value) {
            if (is_array($value)) {
                $productId = (int) ($value['product_id'] ?? 0);
                $qty       = max(1, (int) ($value['qty'] ?? 1));
                $size      = isset($value['size'])    && $value['size']    !== '' ? (string) $value['size']    : null;
                $color     = isset($value['color'])   && $value['color']   !== '' ? (string) $value['color']   : null;
                $quality   = isset($value['quality']) && $value['quality'] !== '' ? (string) $value['quality'] : null;
                $composite = (string) $key;
            } else {
                $productId = (int) $key;
                $qty       = max(1, (int) $value);
                $size      = null;
                $color     = null;
                $quality   = null;
                $composite = $productId . ':::';
            }

            if ($productId <= 0) {
                continue;
            }

            $items[] = [
                'key'        => $composite,
                'product_id' => $productId,
                'qty'        => $qty,
                'size'       => $size,
                'color'      => $color,
                'quality'    => $quality,
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

        $qualityModel = new ProductQuality();

        foreach ($cartRows as $row) {
            $product = $productModel->findByIdWithImage((int) $row['product_id']);
            if (!$product) {
                continue;
            }
            $unitPrice = (float) $product->price;
            if (!empty($row['quality'])) {
                $qRecord = $qualityModel->findByProductAndType((int) $row['product_id'], $row['quality']);
                if ($qRecord && isset($qRecord->price) && $qRecord->price !== null) {
                    $unitPrice = (float) $qRecord->price;
                }
            }
            $lineTotal  = $unitPrice * (int) $row['qty'];
            $subtotal  += $lineTotal;
            $items[]    = [
                'key'       => $row['key'],
                'product'   => $product,
                'qty'       => (int) $row['qty'],
                'size'      => $row['size'],
                'color'     => $row['color'],
                'quality'   => $row['quality'],
                'unitPrice' => $unitPrice,
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
        $size      = trim((string) ($_POST['size']    ?? ''));
        $color     = trim((string) ($_POST['color']   ?? ''));
        $quality   = trim((string) ($_POST['quality'] ?? ''));
        $size      = $size    !== '' ? $size    : null;
        $color     = $color   !== '' ? $color   : null;
        $quality   = $quality !== '' ? $quality : null;

        if ($productId <= 0) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid product.']);
                return;
            }
            $this->flash('error', 'Invalid product.');
            $this->redirectBack(url('products'));
        }

        $sizeModel    = new ProductSize();
        $colorModel   = new ProductColor();
        $qualityModel = new ProductQuality();

        $availableSizes     = $sizeModel->findAvailable($productId);
        $availableColors    = $colorModel->findByProduct($productId);
        $availableQualities = $qualityModel->findByProduct($productId);

        $productHasSizes     = !empty($availableSizes);
        $productHasColors    = !empty($availableColors);
        $productHasQualities = !empty($availableQualities);

        // Size validation
        if ($productHasSizes && $size === null) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Please select a size.']);
                return;
            }
            $this->flash('error', 'Please select a size before adding this product to cart.');
            $this->redirectBack(url('product/' . $productId));
        }

        if ($productHasSizes && $size !== null && !$sizeModel->isAvailable($productId, $size, $quantity)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Selected size is unavailable.']);
                return;
            }
            $this->flash('error', 'Selected size is unavailable for that quantity.');
            $this->redirectBack(url('product/' . $productId));
        }

        // Quality validation
        if ($productHasQualities && $quality === null) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Please select a quality.']);
                return;
            }
            $this->flash('error', 'Please select a quality before adding this product to cart.');
            $this->redirectBack(url('product/' . $productId));
        }

        $validQualityTypes = array_map(fn($q) => $q->quality_type, $availableQualities);
        if ($productHasQualities && $quality !== null && !in_array($quality, $validQualityTypes, true)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Invalid quality selection.']);
                return;
            }
            $this->flash('error', 'Invalid quality selection.');
            $this->redirectBack(url('product/' . $productId));
        }

        // Color validation
        $isHighQuality = $quality !== null && ProductQuality::isHighQuality($quality);

        if ($isHighQuality) {
            // For 220g / 250g: only White and Black are valid
            if ($color === null) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Please select a color (White or Black).']);
                    return;
                }
                $this->flash('error', 'Please select White or Black for this quality.');
                $this->redirectBack(url('product/' . $productId));
            }
            if (!in_array(strtolower($color), ['white', 'black'], true)) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Only White and Black are available for this quality.']);
                    return;
                }
                $this->flash('error', 'Only White and Black are available for this quality.');
                $this->redirectBack(url('product/' . $productId));
            }
        } elseif ($productHasColors && $color === null) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Please select a color.']);
                return;
            }
            $this->flash('error', 'Please select a color before adding this product to cart.');
            $this->redirectBack(url('product/' . $productId));
        } elseif ($productHasColors && $color !== null) {
            $validColorNames = array_map(fn($c) => $c->color_name, $availableColors);
            if (!in_array($color, $validColorNames, true)) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(['success' => false, 'message' => 'Invalid color selection.']);
                    return;
                }
                $this->flash('error', 'Invalid color selection.');
                $this->redirectBack(url('product/' . $productId));
            }
        }

        $cart = Session::get('cart') ?? [];
        $key  = $productId . ':' . ($size ?? '') . ':' . ($color ?? '') . ':' . ($quality ?? '');
        if (!isset($cart[$key])) {
            $cart[$key] = [
                'product_id' => $productId,
                'size'       => $size,
                'color'      => $color,
                'quality'    => $quality,
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
