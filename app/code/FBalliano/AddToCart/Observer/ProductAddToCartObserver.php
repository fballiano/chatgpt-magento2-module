<?php

namespace FBalliano\AddToCart\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Checkout\Model\Cart;

class ProductAddToCartObserver implements ObserverInterface
{
    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * ProductAddToCartObserver constructor.
     *
     * @param ManagerInterface $messageManager
     * @param Cart $cart
     */
    public function __construct(
        ManagerInterface $messageManager,
        Cart $cart
    ) {
        $this->messageManager = $messageManager;
        $this->cart = $cart;
    }

    public function execute(Observer $observer)
    {
        // Get the product that was added to the cart
        $product = $observer->getEvent()->getData('product');

        // Check if the product has an attribute named internal_systems_enabled
        if ($product->hasData('internal_systems_enabled')) {
            // Get the value of the internal_systems_enabled attribute
            $internalSystemsEnabled = $product->getData('internal_systems_enabled');

            // If the value of the internal_systems_enabled attribute is 1, remove the product from the cart
            if ($internalSystemsEnabled == 1) {
                $this->cart->removeItem($observer->getEvent()->getData('quote_item')->getId())->save();
                $this->messageManager->addErrorMessage(__('This product cannot be added to the cart.'));
            }
        }
    }
}
