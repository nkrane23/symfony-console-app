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
use Symfony\Component\Console\Question\ConfirmationQuestion;

// the name of the command
#[AsCommand(
	name: 'app:delete',
	description: 'Deletes an entry in the database',
	hidden: false,
)]

class DeleteCommand extends Command
{
	protected function execute(InputInterface $input, OutputInterface $output) : int
	{
		$helper = $this->getHelper('question');

		// ---- Asks for the ID
		$id = new Question('Please enter the ID of the entry to delete: ', 'Default ID');
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

		// check if entryID exists
		if ($result == 1) {
			$output->writeln('<comment>ID found!</comment>');

			$helper = $this->getHelper('question');
			$question = new ConfirmationQuestion('Confirm y to delete. n to cancel: ', false);

			// if yes - delete it
			if ($helper->ask($input, $output, $question)) {
				$sql = "DELETE FROM student_performance WHERE id=?";
				$stmt = $conn->prepare($sql);
				$stmt->bindValue(1, $entryID);
				$stmt->executeStatement();

				$output->writeln('<info>(ﾉ´ヮ´)ﾉ*: ･ﾟ*:.｡. .｡.:*･゜ﾟ･*</info>');
				$output->writeln('<info>Deleted!</info>');
				$output->writeln('<info>(ﾉ´ヮ´)ﾉ*: ･ﾟ*:.｡. .｡.:*･゜ﾟ･*</info>');
			} else {
				$output->writeln('<comment>Cancelled!</comment>');
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