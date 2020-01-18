<?php

namespace App\Service;

use App\Entity\BaseCompany;
use App\Entity\Company;
use App\Model\ReadFilter;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as ReaderException;

class CompanyService
{
    const IMPORT_CHUNK_SIZE = 2048;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $siren
     *
     * @return BaseCompany|null
     */
    public function getCompanyFromSiren($siren): ?BaseCompany
    {
        return $this->em->getRepository(BaseCompany::class)->findOneBySiren($siren);
    }

    /**
     * @param string $filepath
     * @param null   $format
     *
     * @return int
     * @throws ReaderException
     *
     * @throws Exception
     */
    public function importCompanies(string $filepath, $format = null): int
    {
        if (!$format) {
            $format = IOFactory::identify($filepath);
        }
        $reader     = IOFactory::createReader($format);
        $readFilter = new ReadFilter();
        $readFilter->setColumns(['A', 'B', 'M', 'U', 'AC', 'AK']);
        $reader->setReadFilter($readFilter);
        $startRow   = 2;
        $dictionary = [];
        $i          = 0;
        do {
            $readFilter->setRows($startRow, self::IMPORT_CHUNK_SIZE);
            $spreadsheet = $reader->load($filepath);
            $entries     = $spreadsheet->getActiveSheet()->toArray();
            foreach ($entries as $entry) {
                if (!empty($entry[0])) {
                    if (!array_key_exists($entry[0], $dictionary)) {
                        $dictionary[$entry[0]] = (new BaseCompany())->setSiren($entry[0]);
                        $this->em->persist($dictionary[$entry[0]]);
                    }
                    $company = (new Company())
                        ->setName($entry[36])
                        ->setNic($entry[1])
                        ->setZipcode($entry[20])
                        ->setAddress($entry[12])
                        ->setTown($entry[28]);
                    $dictionary[$entry[0]]->addCompany($company);
                    $i++;
                }
            }
            $startRow += self::IMPORT_CHUNK_SIZE;
        } while (count($entries) > 1);
        $this->em->flush();
        return $i;
    }
}