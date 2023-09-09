<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;
use Doctrine\DBAL\Configuration;

// the name of the command
#[AsCommand(
	name: 'app:create',
	description: 'Creates a new entry in the database',
	hidden: false,
)]

class CreateCommand extends Command
{
	protected function execute(InputInterface $input, OutputInterface $output) : int
	{
		$helper = $this->getHelper('question');

		// ---- Asks for the students name
		$name = new Question('Please enter the students name: ', 'Default Name');
		$name->setValidator(function (string $answer): string {
			if (preg_match('/^[0-9*#+$%&^()!@]+$/', $answer)) {
				throw new \RuntimeException(
					'Oh no! An illegal character was detected! Only letters allowed! 1 try remaining.'
				);
			}

			return $answer;
		});
		$name->setMaxAttempts(2);
		$studentName = $helper->ask($input, $output, $name);
		// ---- end of student name question

		// ---- Asks for the group name
		$groupQuestion = new Question('Please enter the group name: ', 'Default Group');
		$groupQuestion->setValidator(function (string $answer): string {
			if (preg_match('/^[0-9*#+$%&^()!@]+$/', $answer)) {
				throw new \RuntimeException(
					'Oh no! An illegal character was detected! Only letters allowed! 1 try remaining.'
				);
			}

			return $answer;
		});
		$groupQuestion->setMaxAttempts(2);
		$group = $helper->ask($input, $output, $groupQuestion);
		// ---- end of group name question

		// ---- Asks for the subject name
		$subjectQuestion = new Question('Please enter the subject name: ', 'Default Subject');
		$subjectQuestion->setValidator(function (string $answer): string {
			if (preg_match('/^[0-9*#+$%&^()!@]+$/', $answer)) {
				throw new \RuntimeException(
					'Oh no! An illegal character was detected! Only letters allowed! 1 try remaining.'
				);
			}

			return $answer;
		});
		$subjectQuestion->setMaxAttempts(2);
		$subject = $helper->ask($input, $output, $subjectQuestion);
		// ---- end of subject name question

		// ---- Asks for the assessment score
		$scoreQuestion = new Question('Please enter the assessment score: ', '1');
		$scoreQuestion->setValidator(function (string $answer): string {

		// Checks if answer is not a number or a numeric string (ex. user enters a letter),
		// or if the score is not between 1-10
		if(!is_numeric($answer) || ($answer < 1) || ($answer > 10)) {
			throw new \RuntimeException(
				'Oh no! Only numbers between 1-10 allowed! 1 try remaining.'
			);
		} else {
			// if the answer is a number or numeric string, and is between 1-10
			// convert it to a float value
			$float = (float)$answer;
		}
			return $float;
		});
		$scoreQuestion->setMaxAttempts(2);
		$score = $helper->ask($input, $output, $scoreQuestion);
		// ---- end of assessment score question

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

		$sql_table = "CREATE TABLE IF NOT EXISTS `code_test`.`student_performance`
					(`id` INT NOT NULL AUTO_INCREMENT , 
					`name` TEXT NOT NULL , 
					`student_group` TEXT NOT NULL , 
					`subject` TEXT NOT NULL , 
					`date` DATE NOT NULL , 
					`assessment_score` INT NOT NULL , 
					PRIMARY KEY (`id`)) ENGINE = InnoDB;";

		// Creates student_performance table if none exists
		$conn->executeQuery($sql_table);

		// date is set to whatever current date the command is run on
		$sql = "INSERT INTO student_performance (name, student_group, subject, date, assessment_score) VALUES (?, ?, ?, CURRENT_DATE, ?)";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(1, $studentName);
		$stmt->bindValue(2, $group);
		$stmt->bindValue(3, $subject);
		$stmt->bindValue(4, $score);
		$stmt->executeStatement();

		$output->writeln('<info>(ﾉ´ヮ´)ﾉ*: ･ﾟ*:.｡. .｡.:*･゜ﾟ･*</info>');
		$output->writeln('<info>Entry successful!</info>');
		$output->writeln('<info>(ﾉ´ヮ´)ﾉ*: ･ﾟ*:.｡. .｡.:*･゜ﾟ･*</info>');

		return Command::SUCCESS;
	}
}