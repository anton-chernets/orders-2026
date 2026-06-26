<?php

namespace Modules\Order\Livewire;

use App\Contracts\Catalog\ProductRepositoryInterface;
use DomainException;
use Illuminate\View\View;
use Livewire\Component;
use Modules\Order\Actions\PlaceOrderAction;

class PlaceOrderForm extends Component
{
    public string $customerName = '';
    public string $customerEmail = '';

    /** @var array<int, int> product_id => quantity */
    public array $cart = [];

    public function addToCart(int $productId): void
    {
        $this->cart[$productId] = ($this->cart[$productId] ?? 0) + 1;
    }

    public function removeFromCart(int $productId): void
    {
        unset($this->cart[$productId]);
    }

    public function placeOrder(PlaceOrderAction $action): void
    {
        $this->validate([
            'customerName' => 'required|string|max:255',
            'customerEmail' => 'required|email|max:255',
            'cart' => 'required|array|min:1',
        ]);

        $cartItems = collect($this->cart)
            ->map(fn ($qty, $id) => ['product_id' => (int) $id, 'quantity' => $qty])
            ->values()
            ->all();

        try {
            $order = $action->execute($this->customerName, $this->customerEmail, $cartItems);
            session()->flash('success', "Order #{$order->id} placed successfully.");
            $this->reset(['customerName', 'customerEmail', 'cart']);
        } catch (DomainException $e) {
            $this->addError('cart', $e->getMessage());
        }
    }

    public function render(ProductRepositoryInterface $products): View
    {
        return view('order::livewire.place-order-form', [
            'availableProducts' => $products->listAvailable(),
        ]);
    }
}
