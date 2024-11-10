<?php namespace Vankosoft\UsersBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Vankosoft\UsersBundle\Security\UserManager;
use Vankosoft\UsersBundle\Repository\UsersRepository;

#[AsCommand(
    name: 'vankosoft:user:change-password',
    description: 'Change Password of an user.',
    hidden: false
)]
class ChangePasswordCommand extends Command
{
    /** @var UserManager */
    private $userManager;
    
    /** @var UsersRepository */
    private $repository;
    
    public function __construct( UserManager $userManager, UsersRepository $repository )
    {
        parent::__construct();
        
        $this->userManager  = $userManager;
        $this->repository   = $repository;
    }
    
    protected function configure(): void
    {
        $this
            ->setHelp(<<<EOT
The <info>%command.name% username new_password</info>
EOT
            )
            
            ->addArgument( 'username', InputArgument::REQUIRED, 'The username of the user.' )
            ->addArgument( 'password', InputArgument::REQUIRED, 'New Password' )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $username   = $input->getArgument( 'username' );
        $password   = $input->getArgument( 'password' );
        
        $output->writeln([
            'User Creator',
            '==============',
            '',
        ]);
        
        $user = $this->repository->findOneBy(['username' => $username]);
        if ( ! $user ) {
            $output->writeln([
                '',
                '-------------------------------',
                'User with Such username Not Exists!',
                '',
            ]);
            
            return Command::FAILURE;
        }
        
        $this->userManager->encodePassword( $user, $password );
        $this->userManager->saveUser( $user );
        
        $output->writeln([
            '',
            '-------------------------------',
            'Successfully changed the Password!',
            '',
        ]);
        
        return Command::SUCCESS;
    }
}
