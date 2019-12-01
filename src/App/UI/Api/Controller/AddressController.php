<?php

declare(strict_types=1);

namespace App\UI\Api\Controller;

use App\AddressService;
use App\Exception\ApiExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AddressController extends AbstractController
{
    private $addressService;

    public function __construct(AddressService $addressService)
    {
        $this->addressService = $addressService;
    }

    /**
     * @Route("/address")
     */
    public function execute(Request $request)
    {
        try {
            $result = $this->addressService
                ->addressSearch(
                    (array) $request->get('fields', array()),
                    (array) $request->get('filters', array()),
                    (array) $request->get('orders', array()),
                    (string) $request->get('order-direction', 'asc'),
                    (int) $request->get('limit', 20),
                    (int) $request->get('offset', 0)
                );
        } catch (ApiExceptionInterface $e) { // todo use custom error handler
            return $this->json($e->toArray());
        }

        return $this->json($result);
    }
}
