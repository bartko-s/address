<?php

declare(strict_types=1);

namespace App\UI\Api\Controller;

use App\AddressService;
use App\Exception\ApiExceptionInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AddressController extends Controller
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
                    (array) $request->get('fields', []),
                    (array) $request->get('filters', []),
                    (array) $request->get('orders', []),
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
