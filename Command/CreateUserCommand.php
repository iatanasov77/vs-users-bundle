<?php namespace Vankosoft\UsersBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

use Vankosoft\UsersBundle\Model\Interfaces\UserInterface;
use Vankosoft\UsersBundle\Security\UserManager;

#[AsCommand(
    name: 'vankosoft:user:create',
    description: 'Creates a new user.',
    hidden: false
)]
class CreateUserCommand extends Command
{
    /** @var UserManager */
    private $userManager;
    
    public function __construct( UserManager $userManager )
    {
        $this->userManager  = $userManager;
        
        parent::__construct();
    }
    
    protected function configure(): void
    {
        $this
            // the full command description shown when running the command with
            // the "--help" option
            ->setHelp( 'This command allows you to create a user...' )
            
            ->addArgument( 'username', InputArgument::REQUIRED, 'The username of the user.' )
            ->addArgument( 'password', InputArgument::REQUIRED, 'User password' )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ) : int
    {
        $output->writeln([
            'User Creator',
            '==============',
            '',
        ]);
        
        $user = $this->configureNewUser( $input, $output );
        $this->userManager->saveUser( $user );
        
        $output->writeln([
            '',
            '-------------------------------',
            'User successfully generated!',
            '',
        ]);
        
        return Command::SUCCESS;
    }
    
    private function configureNewUser( InputInterface $input, OutputInterface $output ): UserInterface
    {
        $username   = $input->getArgument( 'username' );
        $password   = $input->getArgument( 'password' );
        
        $output->writeln( 'Username: ' . $username );
        $output->writeln( 'Password: ' . $password );
        
        $user   = $this->userManager->createUser( $username, $username, $password );
        
        $user->setEnabled( true );
        $user->setPreferedLocale( 'en_US' );
        
        return $user;
    }
}
