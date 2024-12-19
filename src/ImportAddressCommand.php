<?php

declare(strict_types=1);

namespace App;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class ImportAddressCommand extends Command
{
    private $dbConn;

    public function __construct(Connection $connection)
    {
        $this->dbConn = $connection;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('import:address')
             ->setDescription('Import addresses to the database');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('This action remove previous import from database. Are you sure you want to continue? (type yes or no)', false);

        if (!$helper->ask($input, $output, $question)) {
            return self::FAILURE;
        }

        set_time_limit(0);
        $this->dbConn->getConfiguration()->setSQLLogger(null); //prevent memory leak

        $this->deleteAll($output);
        $this->importVillages($output);
        $this->importStreets($output);

        return self::SUCCESS;
    }

    private function deleteAll(OutputInterface $output)
    {
        $this->dbConn->executeQuery('DELETE FROM address');
        $output->writeln('<info>All rows was deleted</info>');
    }

    private function importVillages(OutputInterface $output)
    {
        $rowNumber = 1;
        $importedItems = 0;

        $inputFileName = __DIR__.'/../import/OBCE.XLSX';
        $spreadsheet = IOFactory::load($inputFileName);

        $worksheet = $spreadsheet->getSheet(0);
        $rows = $worksheet->getHighestRow();

        $progressBar = new ProgressBar($output, $rows);
        $progressBar->setFormat('%current% [%bar%] Elapsed: %elapsed%, Memory usage: %memory%');
        $progressBar->start();

        for ($rowId = 1; $rowId <= $rows; ++$rowId) { //false !== ($data = fgetcsv($handle, 0, ','))) {
            $progressBar->setProgress($rowId);

            $data = $worksheet->rangeToArray(
                'A'.$rowId.':F'.$rowId,
                '',
                true,
                true,
                false
            )[0];

            $city = trim($data[1]);
            $street = trim($data[1]);
            $postcode = trim($data[3]);
            $postOffice = trim($data[5]);

            if (1 === $rowId) {
                if ('obec' != strtolower($street) || 'psc' != strtolower($postcode)
                    || 'obec' != strtolower($city) || 'posta' != strtolower($postOffice)) {
                    $output->writeln('<error>Wrong columns names. Incorrect file or format of source file was changed.</error>');
                    break;
                }
            } else {
                $importedItems += $this->save($street, $city, $postcode, $postOffice);
            }

//            if ($rowNumber > 1 * 1) break;

            if (0 === $rowNumber % 500) {
                gc_collect_cycles(); // prevent memory leak
            }
        }

        $progressBar->finish();
        $output->writeln(array('', sprintf('<info>Success. %s items was imported.</info>', $importedItems)));
    }

    private function importStreets(OutputInterface $output)
    {
        $rowNumber = 1;
        $importedItems = 0;

        $inputFileName = __DIR__.'/../import/ULICE.XLSX';
        $spreadsheet = IOFactory::load($inputFileName);

        $worksheet = $spreadsheet->getSheet(0);
        $rows = (int) $worksheet->getHighestRow();

        $progressBar = new ProgressBar($output, $rows);
        $progressBar->setFormat('%current% [%bar%] Elapsed: %elapsed%, Memory usage: %memory%');
        $progressBar->start();

        for ($rowId = 1; $rowId <= $rows; ++$rowId) { //false !== ($data = fgetcsv($handle, 0, ','))) {
            $progressBar->setProgress($rowId);

            $data = $worksheet->rangeToArray(
                'A'.$rowId.':G'.$rowId,
                '',
                true,
                true,
                false
            )[0];

            $city = trim($data[6]);
            $street = trim($data[1]);
            $postcode = trim($data[2]);
            $postOffice = trim($data[4]);

            if (1 === $rowId) {
                if ('ulica' != strtolower($street) || 'psc' != strtolower($postcode)
                    || 'obce' != strtolower($city) || 'posta' != strtolower($postOffice)) {
                    $output->writeln('<error>Wrong columns names. Incorrect file or format of source file was changed.</error>');
                    break;
                }
            } else {
                $importedItems += $this->save($street, $city, $postcode, $postOffice);
            }

//            if ($rowNumber > 1 * 1) break;

            if (0 === $rowNumber % 500) {
                gc_collect_cycles(); // prevent memory leak
            }
        }

        $progressBar->finish();
        $output->writeln(array('', sprintf('<info>Success. %s items was imported.</info>', $importedItems)));
    }

    /**
     * @return int Number of inserted rows
     */
    private function save(string $street, string $city, string $postcode, string $postOffice): int
    {
        $validData = array();

        $city = preg_replace('|([^ ])(-)([^ ])|', '$1 - $3', $city);
        if (0 === strlen($city)) {
            $errorMessage = 'City cannot be empty'; //todo log somewhere
            return 0;
        } else {
            $validData['city'] = $city;
        }

        if (0 === strlen($street)) {
            $errorMessage = 'Street cannot be empty'; //todo log somewhere
            return 0;
        } else {
            $validData['street'] = $street;
        }

        $postcode = str_replace(' ', '', $postcode);
        if (5 != strlen($postcode)) {
            $errorMessage = 'Postcode wrong format'; //todo log somewhere
            return 0;
        } else {
            $validData['postcode'] = $postcode;
        }

        if (0 === strlen($postOffice)) {
            $errorMessage = 'Post Office cannot be empty'; //todo log somewhere
            return 0;
        } else {
            $validData['postOffice'] = $postOffice;
        }

        $sql = 'INSERT INTO address (street, postcode, city, post_office, street_search, postcode_search, city_search, post_office_search)'
            ." VALUES (:street, :postcode, :city, :post_office, to_tsvector('simple', :street_search), to_tsvector('simple', :postcode_search), to_tsvector('simple', :city_search), to_tsvector('simple', :post_office_search))";

        $binds = array();
        $binds['street'] = $validData['street'];
        $binds['postcode'] = $validData['postcode'];
        $binds['city'] = $validData['city'];
        $binds['post_office'] = $validData['postOffice'];
        $binds['street_search'] = Utils::makeNGrams(Utils::removeDiacritics($validData['street']), 1);
        $binds['postcode_search'] = Utils::makeNGrams(Utils::removeDiacritics($validData['postcode']), 1);
        $binds['city_search'] = Utils::makeNGrams(Utils::removeDiacritics($validData['city']), 1);
        $binds['post_office_search'] = Utils::makeNGrams(Utils::removeDiacritics($validData['postOffice']), 1);

        try {
            $this->dbConn->executeQuery($sql, $binds);

            return 1;
        } catch (UniqueConstraintViolationException $e) {
            // this is OK
        }

        return 0;
    }
}
