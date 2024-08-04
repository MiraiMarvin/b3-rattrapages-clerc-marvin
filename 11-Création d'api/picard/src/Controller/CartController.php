<?php
namespace App\Controller;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CartController extends AbstractController
{
    
    #[Route('/api/carts/{cartId}/products/{productId}', name: 'add_product_to_cart', methods: ['POST'])]
    public function addProductToCart(int $cartId, int $productId, EntityManagerInterface $em): JsonResponse
    {
        $cart = $em->getRepository(Cart::class)->find($cartId);
        $product = $em->getRepository(Product::class)->find($productId);

        if (!$cart) {
            return $this->json(['error' => 'Cart not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if (!$product) {
            return $this->json(['error' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $cart->addProduct($product);
        $em->persist($cart);
        $em->flush();

        return $this->json(['status' => 'Product added to cart'], JsonResponse::HTTP_OK);
    }

    
    #[Route('/api/carts/{cartId}/products/{productId}', name: 'remove_product_from_cart', methods: ['DELETE'])]
    public function removeProductFromCart(int $cartId, int $productId, EntityManagerInterface $em): JsonResponse
    {
        $cart = $em->getRepository(Cart::class)->find($cartId);
        $product = $em->getRepository(Product::class)->find($productId);

        if (!$cart) {
            return $this->json(['error' => 'Cart not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        if (!$product) {
            return $this->json(['error' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        $cart->removeProduct($product);
        $em->persist($cart);
        $em->flush();

        return $this->json(['status' => 'Product removed from cart'], JsonResponse::HTTP_OK);
    }

    
    #[Route('/api/carts/{cartId}/checkout', name: 'checkout_cart', methods: ['POST'])]
    public function checkoutCart(int $cartId, EntityManagerInterface $em): JsonResponse
    {
        $cart = $em->getRepository(Cart::class)->find($cartId);

        if (!$cart) {
            return $this->json(['error' => 'Cart not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        
        $cart->setStatus('validated');
        $em->persist($cart);
        $em->flush();

        return $this->json(['status' => 'Cart validated'], JsonResponse::HTTP_OK);
    }

    #[Route('/api/products/{productId}/rating', name: 'rate_product', methods: ['POST'])]
    public function rateProduct(int $productId, Request $request, EntityManagerInterface $em): JsonResponse
    {
        
        $rating = $request->request->get('rating');
        if ($rating === null || !is_numeric($rating) || $rating < 0 || $rating > 5) {
            return $this->json(['error' => 'Invalid rating'], JsonResponse::HTTP_BAD_REQUEST);
        }

        $product = $em->getRepository(Product::class)->find($productId);
        if (!$product) {
            return $this->json(['error' => 'Product not found'], JsonResponse::HTTP_NOT_FOUND);
        }

        
        $product->setRating((float) $rating);
        $em->persist($product);
        $em->flush();

        return $this->json(['status' => 'Product rated'], JsonResponse::HTTP_OK);
    }
}
