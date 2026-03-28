<?php

/**
 * app/controllers/WishlistController.php
 * Account wishlist actions.
 */
class WishlistController extends Controller
{
    public function index(): void
    {
        $user = Session::user();
        $wishlistModel = new Wishlist();

        $items = $wishlistModel->findByCustomer((int) $user['id']);

        $this->render('account.wishlist', [
            'pageTitle' => 'My Wishlist — ' . APP_NAME,
            'user'      => $user,
            'items'     => $items,
        ]);
    }

    public function add(): void
    {
        $this->verifyCsrf();

        $user = Session::user();
        $productId = (int) $this->post('product_id', 0);

        if ($productId <= 0) {
            $this->redirectBack(url('products'));
        }

        (new Wishlist())->add((int) $user['id'], $productId);
        $this->flash('success', 'Added to your wishlist.');
        $this->redirectBack(url('products'));
    }

    public function remove(): void
    {
        $this->verifyCsrf();

        $user = Session::user();
        $productId = (int) $this->post('product_id', 0);

        if ($productId > 0) {
            (new Wishlist())->remove((int) $user['id'], $productId);
            $this->flash('success', 'Removed from your wishlist.');
        }

        $this->redirectBack(url('account/wishlist'));
    }

    public function toggle(): void
    {
        $this->verifyCsrf();

        $user = Session::user();
        $productId = (int) $this->post('product_id', 0);

        if ($productId <= 0) {
            $this->redirectBack(url('products'));
        }

        $wishlist = new Wishlist();
        if ($wishlist->has((int) $user['id'], $productId)) {
            $wishlist->remove((int) $user['id'], $productId);
            $this->flash('success', 'Removed from your wishlist.');
        } else {
            $wishlist->add((int) $user['id'], $productId);
            $this->flash('success', 'Added to your wishlist.');
        }

        $this->redirectBack(url('products'));
    }
}
