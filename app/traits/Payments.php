<?php
/**
 * =======================================================================================
 *                           GemFramework (c) GemPixel
 * ---------------------------------------------------------------------------------------
 *  This software is packaged with an exclusive framework as such distribution
 *  or modification of this framework is not allowed before prior consent from
 *  GemPixel. If you find that this framework is packaged in a software not distributed
 *  by GemPixel or authorized parties, you must not use this software and contact GemPixel
 *  at https://gempixel.com/contact to inform them of this misuse.
 * =======================================================================================
 *
 * @package GemPixel\Premium-URL-Shortener
 * @author GemPixel (https://gempixel.com)
 * @license https://gempixel.com/licenses
 * @link https://gempixel.com
 */

namespace Traits;

use Core\DB;
use Core\Helper;
use Core\Request;
use Helpers\Payments\Bank;
use Helpers\Payments\Paypal;
use Helpers\Payments\Stripe;
use Helpers\Payments\PaypalApi;
use Helpers\Payments\Paddle;
use Helpers\Payments\PayStack;

trait Payments {

  /**
   * Payment List
   *
   * @author GemPixel <https://gempixel.com>
   * @version 6.0
   * @param string|null $type
   * @param string|null $action
   * @return void
   */
    public function processor($type = null, $action = null){

        $list = [
			'paypal' => [
				'provider' => 'Paypal Single Payment',
				'name' => e('PayPal'),
                'config' => ['single' => true, 'subscription' => false],
				'settings' => [PayPal::class, 'settings'],
				'save' => null,
				'checkout' => [PayPal::class, 'checkout'],
				'payment' => [PayPal::class, 'payment'],
				'subscription' => null,
				'webhook' => [PayPal::class, 'webhook'],
				'createplan' => null,
				'updateplan' => null,
				'syncplan' => null,
				'cancel' => null,
				'createcoupon' => null,
				'createtax' => null
			],
            'stripe' => [
				'provider' => 'Stripe',
				'name' => e('Credit Card'),
                'config' => ['single' => true, 'subscription' => true],
				'settings' => [Stripe::class, 'settings'],
				'save' => null,
				'checkout' => [Stripe::class, 'checkout'],
				'payment' => [Stripe::class, 'payment'],
				'subscription' => [Stripe::class, 'subscription'],
				'webhook' => [Stripe::class, 'webhook'],
				'createplan' => [Stripe::class, 'createplan'],
				'updateplan' => [Stripe::class, 'updateplan'],
				'syncplan' => [Stripe::class, 'syncplan'],
				'cancel' => [Stripe::class, 'cancel'],
				'createcoupon' => [Stripe::class, 'createcoupon'],
				'createtax' => [Stripe::class, 'createtax'],
				'manage' => [Stripe::class, 'manage']
			],
			'paypalapi' => [
				'provider' => 'PayPal API',
				'name' => e('PayPal'),
                'config' => ['single' => true, 'subscription' => true],
				'settings' => [PaypalApi::class, 'settings'],
				'save' => [PaypalApi::class, 'save'],
				'checkout' => [PaypalApi::class, 'checkout'],
				'payment' => [PaypalApi::class, 'payment'],
				'subscription' => [PaypalApi::class, 'subscription'],
				'webhook' => [PaypalApi::class, 'webhook'],
				'createplan' => [PaypalApi::class, 'createplan'],
				'updateplan' => [PaypalApi::class, 'updateplan'],
				'syncplan' => [PaypalApi::class, 'syncplan'],
				'cancel' => [PaypalApi::class, 'cancel'],
				'createcoupon' => null,
				'createtax' => null
			],
			'paddle' => [
				'provider' => 'Paddle',
				'name' => e('Credit Card'),
				'config' => ['single' => true, 'subscription' => true],
				'settings' => [Paddle::class, 'settings'],
				'save' => null,
				'checkout' => [Paddle::class, 'checkout'],
				'payment' => [Paddle::class, 'payment'],
				'subscription' => [Paddle::class, 'subscription'],
				'webhook' => [Paddle::class, 'webhook'],
				'createplan' => null,
				'updateplan' => null,
				'syncplan' => null,
				'cancel' => [Paddle::class, 'cancel'],
				'createcoupon' => null,
				'createtax' => null,
				'manage' => [Paddle::class, 'manage']
			],
			'paystack' => [
				'provider' => 'PayStack',
				'name' => e('Credit Card'),
				'config' => ['single' => true, 'subscription' => true],
				'settings' => [PayStack::class, 'settings'],
				'save' => null,
				'checkout' => [PayStack::class, 'checkout'],
				'payment' => [PayStack::class, 'payment'],
				'subscription' => [PayStack::class, 'subscription'],
				'webhook' => [PayStack::class, 'webhook'],
				'createplan' => [PayStack::class, 'createplan'],
				'updateplan' => [PayStack::class, 'updateplan'],
				'syncplan' => [PayStack::class, 'syncplan'],
				'cancel' => [PayStack::class, 'cancel'],
				'createcoupon' => null,
			],
			'bank' => [
				'provider' => e('Bank Transfer'),
				'name' => e('Bank Transfer'),
				'description' => e('Transfer payments via your bank'),
                'config' => ['single' => true, 'subscription' => false],
				'settings' => [Bank::class, 'settings'],
				'save' => null,
				'checkout' => [Bank::class, 'checkout'],
				'payment' => [Bank::class, 'payment'],
				'subscription' => null,
				'webhook' => null,
				'createplan' => null,
				'updateplan' => null,
				'syncplan' => null,
				'cancel' => null,
				'createcoupon' => null,
				'createtax' => null
            ]
		];

		if($extended = \Core\Plugin::dispatch('payment.extend')){
			foreach($extended as $fn){
				$list = array_merge($list, $fn);
			}
		}

		if($type && $action && isset($list[$type][$action])) return $list[$type][$action];

		if(isset($list[$type])) return $list[$type];

		return $list;
    }

}