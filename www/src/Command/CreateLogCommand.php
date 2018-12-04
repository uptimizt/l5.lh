<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Schema\Table;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Console commandf for generate DB
 *
 * Example: php bin/console app:create-log
 */
class CreateLogCommand extends ContainerAwareCommand
{
    /**
     *  the name of the command (the part after "bin/console")
     */
    protected static $defaultName = 'app:create-log';

    /**
     * The Configure
     */
    protected function configure()
    {
      $this
      ->setDescription('Creates a new log data.')
      ->setHelp('This command allows you to create a log...')
      ;
    }

    /**
     * The Execute
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {

      $em = $this->getContainer()->get('doctrine')->getEntityManager();
      $conn = $em->getConnection();
      $sm = $conn->getSchemaManager();

      $message = array(
        'Log Creator',
        '============',
        '',
      );

      $io = new SymfonyStyle($input, $output);

      if ($sm->tablesExist(array('log')) != true) {
        $io->section('Create table');

        $table = new Table('log');

        $table->addColumn('id', 'integer', array(
            'autoincrement' => true,
        ));
        $table->setPrimaryKey(array('id'));
        $table->addColumn('ts', 'datetime', ['default' => 'CURRENT_TIMESTAMP']);
        $table->addColumn('type', 'integer');
        $table->addColumn('message', 'text');
        $table->addIndex(['id', 'ts', 'type']);
        $table->addIndex(['id']);
        $table->addIndex(['ts']);
        $table->addIndex(['type']);

        $sm->createTable($table); // save to DB
      }

      $faker = \Faker\Factory::create();

      $sql = "INSERT INTO log(message, type) VALUES (:message, :type)";

      $count = 3000000;

      $io->section('Generate ' . $count . ' rows for log table');

      $io->progressStart($count);

      for ($i=0; $i < $count; $i++) {
        $name = $faker->text;
        $res = $conn->executeUpdate($sql, ['message' => $name, 'type' => rand(0, 9)]);
        if($i%1000 == 0){
          $io->progressAdvance(1000);
        }
      }

      $io->progressFinish();
      $io->success('All good - ' . $count . ' rows created');

    }
}
