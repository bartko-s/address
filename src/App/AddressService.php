<?php

declare(strict_types=1);

namespace App;

use App\Exception\BadRequestException;
use Doctrine\DBAL\Connection;

class AddressService
{
    private $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    private $allowedFields = ['street', 'city', 'postcode', 'post_office'];
    private $allowedFilters = ['street', 'city', 'postcode', 'post_office'];
    private $allowedOrders = ['street', 'city', 'postcode', 'post_office'];
    private $allowedDirections = ['asc', 'desc'];
    private $maxLimit = 1000;

    /**
     * @param array  $fields
     * @param array  $filters
     * @param array  $orders
     * @param string $orderDirection
     * @param int    $limit
     * @param int    $offset
     *
     * @return array
     *
     * @throws BadRequestException
     */
    public function addressSearch(
        array $fields = [],
        array $filters = [],
        array $orders = [],
        string $orderDirection = 'asc',
        int $limit = 20,
        int $offset = 0
    ): array {
        if (0 === count($fields)) {
            $fields = $this->allowedFields;
        } else {
            $notAllowedFields = array_diff($fields, $this->allowedFields);
            if (count($notAllowedFields) > 0) {
                throw new BadRequestException(
                    sprintf('Field "%s" is not allowed', implode(', ', $notAllowedFields))
                );
            }
        }

        $notAllowedFilters = array_diff(array_keys($filters), $this->allowedFilters);
        if (count($notAllowedFilters) > 0) {
            throw new BadRequestException(
                sprintf('Filter "%s" is not allowed', implode(', ', $notAllowedFilters))
            );
        }

        if (0 === count($orders)) {
            $orders = $this->allowedOrders;
        } else {
            $notAllowedOrders = array_diff($orders, $this->allowedOrders);
            if (count($notAllowedOrders) > 0) {
                throw new BadRequestException(
                    sprintf('Order "%s" is not allowed', implode(', ', $notAllowedOrders))
                );
            }
        }
        $orders = array_intersect($orders, $fields);

        if (!in_array(strtolower($orderDirection), $this->allowedDirections)) {
            throw new BadRequestException(
                sprintf('Wrong order direction "%s"', implode(', ', $orderDirection))
            );
        }

        if ($limit <= 0 || $limit > $this->maxLimit) {
            throw new BadRequestException(
                sprintf('Wrong limit "%s"', implode(', ', $limit))
            );
        }

        if ($offset < 0) {
            throw new BadRequestException(
                sprintf('Wrong offset "%s"', implode(', ', $offset))
            );
        }

        $data = array();
        $sql = 'SELECT '.implode(', ', $fields)
          .' FROM address';

        if (count($filters) > 0) {
            $where = [];
            foreach ($filters as $key => $value) {
                $where[] .= sprintf("%s_search @@ to_tsquery('simple', :__%s)", $key, $key);
                $data['__'.$key] = Utils::createQuerySearchString($value);
            }

            $sql .= ' WHERE '.implode(' AND ', $where);
        }

        $sql .= ' GROUP BY '.implode(', ', $fields);

        if (count($orders) > 0) {
            $sql .= ' ORDER BY '.implode(', ', $orders).' '.strtoupper($orderDirection);
        }

        $sql .= ' LIMIT :limit OFFSET :offset';
        $data['limit'] = $limit;
        $data['offset'] = $offset;

        return $this->conn
            ->executeQuery($sql, $data)
            ->fetchAll();
    }
}
