<?php namespace Vankosoft\UsersBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Style\SymfonyStyle;

use Doctrine\Persistence\ManagerRegistry;
use Vankosoft\UsersBundle\Repository\UserRolesRepository;

#[AsCommand(
    name: 'vankosoft:user:remove-bad-users',
    description: 'Remove Bad Users (Unverified Users) in Role and Date Created.',
    hidden: false
)]
class RemoveBadUsersCommand extends Command
{
    /** @var ManagerRegistry */
    private $doctrine;
    
    /** @var UserRolesRepository */
    private $rolesRepository;
    
    public function __construct( ManagerRegistry $doctrine, UserRolesRepository $rolesRepository )
    {
        parent::__construct();
        
        $this->doctrine         = $doctrine;
        $this->rolesRepository  = $rolesRepository;
    }
    
    protected function configure(): void
    {
        $this
            ->setHelp(<<<EOT
The <info>%command.name% -r=ROLE_REGISTERED_USER -d=2025-03-03</info> Command remove users from role thats are not verified
EOT
            )
            
            ->addOption( 'role', 'r', InputOption::VALUE_REQUIRED, 'User Role from which to get users', null )
            ->addOption( 'before-date', 'd', InputOption::VALUE_REQUIRED, 'Delete users created before this date', null )
        ;
    }
    
    protected function execute( InputInterface $input, OutputInterface $output ): int
    {
        $style  = new SymfonyStyle( $input, $output );
        
        $inputRole  = $input->getOption( 'role' );
        $inputDate  = $input->getOption( 'before-date' );
        $beforeDate = \DateTime::createFromFormat( 'y-m-d', $inputDate );
        
        $countRemoved   = 0;
        $usersRole      = $this->rolesRepository->findOneBy( ['role' => $inputRole] );
        foreach ( $usersRole->getUsers() as $user ) {
            if ( ! $user->isVerified() && $user->getCreatedAt() < $beforeDate ) {
                $countRemoved++;
                $this->doctrine->getManager()->remove( $user );
            }
        }
        
        $this->doctrine->getManager()->flush();
        
        $style->success( \sprintf( 'Removed %d users Successfully !!!', $countRemoved ) );
       
        return Command::SUCCESS;
    }
}
