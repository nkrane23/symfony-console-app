<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;
use Doctrine\DBAL\Configuration;

#[AsCommand(
	name: 'app:read:average-group',
	description: 'Reads and returns the average scores by group',
	hidden: false,
)]

class ReadCommandAvgGroup extends Command
{
	protected function execute(InputInterface $input, OutputInterface $output) : int
	{
		/* ======= DB Connection ... ======= */
		$configuration = new Configuration();
		$configuration->setSchemaManagerFactory(new DefaultSchemaManagerFactory());

		$conn = DriverManager::getConnection(
			[
				'dbname' => 'database_name',
				'user' => 'db_username',
				'password' => 'db_password',
				'host' => 'db_host_location',
				'driver' => 'mysqli'
			],
			$configuration,
		);
		/* ======= END DB Connection ... ======= */

		$sql = "SELECT subject, AVG(assessment_score) 'Average Score' FROM `student_performance` GROUP BY student_group";
		$yield = $conn->executeQuery($sql);

		$results = $yield->fetchAllAssociative();

		$output->writeln('<info>(ﾉ´ヮ´)ﾉ*: ･ﾟ*:.｡. .｡.:*･゜ﾟ･*</info>');
		$output->writeln('<info>Average score by group successful!</info>');
		$output->writeln('<info>(ﾉ´ヮ´)ﾉ*: ･ﾟ*:.｡. .｡.:*･゜ﾟ･*</info>');
		$output->writeln('');

		foreach ($results as $result) {
			$output->writeln('<comment>' . $result['student_group'] . ' : ' . $result['Average Score'] . '</comment>');
		}

		return Command::SUCCESS;
	}
}