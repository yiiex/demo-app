<?php

namespace App\Console;

use App\Models\User;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Yiisoft\Yii\Console\ExitCode;

#[AsCommand(
    name: 'user',
    description: 'create user',
)]
class UserCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $helper = $this->getHelper('question');

        $emailQuestion = new Question('Enter email: ');
        $emailQuestion->setValidator(function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException('Invalid email format');
            }
            return $answer;
        });
        $email = $helper->ask($input, $output, $emailQuestion);

        $firstNameQuestion = new Question('Enter first name: ');
        $firstNameQuestion->setValidator(function ($answer) {
            if (empty(trim($answer))) {
                throw new \RuntimeException('First name cannot be empty');
            }
            return trim($answer);
        });
        $firstName = $helper->ask($input, $output, $firstNameQuestion);

        $lastNameQuestion = new Question('Enter last name: ');
        $lastNameQuestion->setValidator(function ($answer) {
            if (empty(trim($answer))) {
                throw new \RuntimeException('Last name cannot be empty');
            }
            return trim($answer);
        });
        $lastName = $helper->ask($input, $output, $lastNameQuestion);

        $passwordQuestion = new Question('Enter password: ');
        $passwordQuestion->setHidden(true);
        $passwordQuestion->setHiddenFallback(false);
        $passwordQuestion->setValidator(function ($answer) {
            if (strlen($answer) < 6) {
                throw new \RuntimeException('Password must be at least 6 characters');
            }
            return $answer;
        });
        $password = $helper->ask($input, $output, $passwordQuestion);

        $confirmPasswordQuestion = new Question('Confirm password: ');
        $confirmPasswordQuestion->setHidden(true);
        $confirmPasswordQuestion->setHiddenFallback(false);
        $confirmPasswordQuestion->setValidator(function ($answer) use ($password) {
            if ($answer !== $password) {
                throw new \RuntimeException('Passwords do not match');
            }
            return $answer;
        });
        $helper->ask($input, $output, $confirmPasswordQuestion);

        $roleQuestion = new Question('Enter role [user]: ', 'user');
        $roleQuestion->setAutocompleterValues(['user', 'admin']);
        $role = $helper->ask($input, $output, $roleQuestion);

        $output->writeln([
            '',
            'Summary:',
            '---------',
            "Email: $email",
            "Name: $firstName $lastName",
            "Role: $role",
            '',
        ]);

        $confirmQuestion = new ConfirmationQuestion('Create this user? (yes/no) [yes]: ', true);
        if (!$helper->ask($input, $output, $confirmQuestion)) {
            $output->writeln('<error>User creation cancelled</error>');
            return ExitCode::OK;
        }

        $user = new User('insert');
        $user->setAttributes([
            'email' => $email,
            'password' => $password,
            'password_confirm' => $password,
            'role' => $role,
            'first_name' => $firstName,
            'last_name' => $lastName,
        ]);

        if ($user->validate() && $user->withHashedPassword()->save(false)) {
            $output->writeln('<info>✓ User created successfully!</info>');
            $output->writeln("User ID: {$user->id}");
            return ExitCode::OK;
        } else {
            $output->writeln('<error>✗ Failed to create user:</error>');
            foreach ($user->getErrors() as $field => $errors) {
                $output->writeln("  - $field: " . implode(', ', $errors));
            }
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}
