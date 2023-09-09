<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Schema\DefaultSchemaManagerFactory;
use Doctrine\DBAL\Configuration;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;

// the name of the command
#[AsCommand(
	name: 'app:update',
	description: 'Updates an existing entry in the database',
	hidden: false,
)]

class UpdateCommand extends Command
{
	function success(OutputInterface $output) : void {
		$output->writeln('<info>(ﾉ´ヮ´)ﾉ*: ･ﾟ*:.｡. .｡.:*･゜ﾟ･*</info>');
		$output->writeln('<info>Update successful!</info>');
		$output->writeln('<info>(ﾉ´ヮ´)ﾉ*: ･ﾟ*:.｡. .｡.:*･゜ﾟ･*</info>');
		$output->writeln('');
	}

	protected function execute(InputInterface $input, OutputInterface $output) : int
	{
		$helper = $this->getHelper('question');

		// ---- Asks for the ID
		$id = new Question('Please enter the ID of the entry to update: ', 'Default ID');
		$id->setValidator(function (string $answer): string {
			if (preg_match('/^[a-zA-Z*#+$%&^()!@]+$/', $answer)) {
				throw new \RuntimeException(
					'Oh no! An illegal character was detected! Only numbers allowed! 1 try remaining.'
				);
			}

			return $answer;
		});
		$id->setMaxAttempts(2);
		$entryID = $helper->ask($input, $output, $id);

		/* ======= DB Connection ======= */
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
		/* ======= END DB Connection ======= */

		$sql = "SELECT * FROM student_performance WHERE id = ?";
		$stmt = $conn->prepare($sql);
		$stmt->bindValue(1, $entryID);
		$result = $stmt->executeStatement();

		if ($result == 1) {
			// ask user which field they want to update - they pick from a list
			$field = new Question('Match found! Which field should be updated?: ', 'Default Field');
			$helperish = $this->getHelper('question');
			$question = new ChoiceQuestion(
				'Match found! Which field should be updated?',
				['Name', 'Student Group', 'Subject', 'Assessment Score'],
				0
			);
			$question->setMaxAttempts(2);
			$question->setErrorMessage('Option %s is invalid.');
			$field = $helperish->ask($input, $output, $question);
			$output->writeln('You have selected: '. $field);

			// If name selected - ask for new name and update
			if ($field == 'Name') {
				// [0] Name
				$name = new Question('Please enter the new name: ', 'Default Name');
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

				// update it!
				$sql = "UPDATE student_performance SET id=?, name=? WHERE id=?";
				$stmt = $conn->prepare($sql);
				$stmt->bindValue(1, $entryID);
				$stmt->bindValue(2, $studentName);
				$stmt->bindValue(3, $entryID);
				$stmt->executeStatement();

				// Success Message
				$this->success($output);

			} elseif ($field == 'Student Group') {
				// [1] Student Group
				$groupQuestion = new Question('Please enter the new student group name: ', 'Default Student Group Name');
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

				// update it!
				$sql = "UPDATE student_performance SET id=?, student_group=? WHERE id=?";
				$stmt = $conn->prepare($sql);
				$stmt->bindValue(1, $entryID);
				$stmt->bindValue(2, $group);
				$stmt->bindValue(3, $entryID);
				$stmt->executeStatement();

				// Success Message
				$this->success($output);

			} elseif ($field == 'Subject') {
				// [2] Subject
				$subjectQuestion = new Question('Please enter the new subject name: ', 'Default Subject Name');
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

				// update it!
				$sql = "UPDATE student_performance SET id=?, subject=? WHERE id=?";
				$stmt = $conn->prepare($sql);
				$stmt->bindValue(1, $entryID);
				$stmt->bindValue(2, $subject);
				$stmt->bindValue(3, $entryID);
				$stmt->executeStatement();

				// Success Message
				$this->success($output);

			} elseif ($field == 'Assessment Score') {
				// [3] Assessment Score
				$scoreQuestion = new Question('Please enter the new assessment score: ', '1');
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

				// update it!
				$sql = "UPDATE student_performance SET id=?, assessment_score=? WHERE id=?";
				$stmt = $conn->prepare($sql);
				$stmt->bindValue(1, $entryID);
				$stmt->bindValue(2, $score);
				$stmt->bindValue(3, $entryID);
				$stmt->executeStatement();

				// Success Message
				$this->success($output);
			}

		} elseif ($result == 0) {
			// if $entryID doesn't exist, output message saying it doesn't exist and quit
			throw new \RuntimeException(
				'The entered ID doesnt exist!'
			);
		}
		return Command::SUCCESS;
	}
}