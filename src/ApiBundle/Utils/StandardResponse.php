<?php

namespace ApiBundle\Utils;

use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializationContext;

/**
 * StandardResponse Class
 */
class StandardResponse
{
    /**
     * Creates and json-encodes the response
     *
     * @param object $response response array
     *
     * @return Json
     */
    public function setResponse($response)
    {
        $context = new SerializationContext();
        $context->setSerializeNull(true);

        $serializer = SerializerBuilder::create()->build();
        $data = $serializer->serialize($response, 'json', $context);

        return $data;
    }
}
