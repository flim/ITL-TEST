<?php

namespace App\Controller;

use App\Service\CompanyService;
use App\Service\JsonSerializer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

class CompanyController extends AbstractController
{
    /**
     * @var CompanyService
     */
    private $companyService;
    private $serializer;

    public function __construct(CompanyService $companyService, JsonSerializer $serializer)
    {
        $this->companyService = $companyService;
        $this->serializer     = $serializer;
    }

    /**
     * @Route("/companies/{siren}", name="company", methods={"get"})
     * @param $siren
     *
     * @return JsonResponse
     */
    public function getCompany(string $siren)
    {
        $company = $this->companyService->getCompanyFromSiren($siren);
        if (null == $company) {
            return new JsonResponse([], 404);
        }

        try {
            $data = $this->serializer->normalize($company, 'json');
            return new JsonResponse($data);
        } catch (ExceptionInterface    $e) {
            return new JsonResponse([
                'code' => $e->getCode(),
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
