<?php

namespace App\Service;

use App\Entity\BaseCompany;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class JsonSerializer extends Serializer
{
    public function __construct()
    {
        $encoders = [new XmlEncoder(), new JsonEncoder()];
        $defaultContext = [
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function (BaseCompany $object) {
                return $object->getId();
            },
        ];
        $normalizers = [new ObjectNormalizer(null, null, null, null, null, null, $defaultContext)];
        parent::__construct($normalizers, $encoders);
    }
}