<?php

namespace App\Command;

use App\Service\CompanyService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use ZipArchive;

class ItlSirenImportCommand extends Command
{
    const REMOTE_FILEPATH = 'http://files.data.gouv.fr/sirene/sirene_2018088_E_Q.zip';
    const FORMAT_MAPPING  = ['csv' => 'Csv'];

    protected static $defaultName = 'itl:siren:import';

    private $companyService;
    /**
     * @var string
     */
    private $extractDest;

    public function __construct(string $extractDest, CompanyService $companyService)
    {
        $this->companyService = $companyService;
        $this->extractDest    = $extractDest;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Import siren list')
            ->addArgument('path', InputArgument::REQUIRED, 'Filepath : eg. ' . self::REMOTE_FILEPATH)
            ->addOption('format', 'f', InputOption::VALUE_OPTIONAL, 'Format of the file (csv)', 'Csv');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Importing siren data');

        // Check folders permissions
        $fs = new Filesystem();
        if (!$fs->exists($this->extractDest)) {
            $io->text('Creating folder(s)');
            $fs->mkdir($this->extractDest);
        }

        // Download remote file
        $localZipFile = $this->extractDest . 'data.zip';
        $remoteFile   = $input->getArgument('path');
        $io->section('Download remote file : ' . $remoteFile);
        $remoteFileContent  = @file_get_contents($remoteFile);
        if ($remoteFileContent === FALSE) {
            $io->error('Remote file not found : ' . $remoteFile);
            return 1;
        }

        // Extract downloaded zip file
        $io->section('Extract downloaded zip file');
        if (file_put_contents($localZipFile, $remoteFileContent)) {
            $zip = new ZipArchive();
            if ($zip->open($localZipFile) === TRUE) {
                $zip->extractTo($this->extractDest);
                $zip->close();
                $format = $input->getOption('format');
                $count  = $this->importData($format, $io);
                $io->success("Import $count companies successfully!");
                $fs->remove($this->extractDest);
                return 0;
            } else {
                $io->error("Unable to extract downloaded file : $localZipFile");
            }
        } else {
            $io->error("Unable to save downloaded file.");
        }
        return 1;
    }

    protected function importData(string $format, SymfonyStyle $io): int
    {
        $finder = new Finder();
        $finder->in($this->extractDest);
        $csvFiles = $finder->files()->name('*.csv');
        if ($csvFiles->count() == 1) {
            $csvFilePath = array_key_first(iterator_to_array($csvFiles));
            $io->section('Importing csv data from : ' . $csvFilePath);
            if (array_key_exists(strtolower($format), self::FORMAT_MAPPING)) {
                $format = self::FORMAT_MAPPING[strtolower($format)];
            }
            return $this->companyService->importCompanies($csvFilePath, $format);
        } else {
            $io->error("There is more than one csv file.");
        }
        return 0;
    }
}
