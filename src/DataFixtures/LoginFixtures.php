<?php

namespace App\DataFixtures;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;


class LoginFixtures extends Fixture
{
    private $passwordEncoder;
public function __construct(UserPasswordEncoderInterface $passwordEncoder)
{
$this->passwordEncoder = $passwordEncoder;
}
    public function load(ObjectManager $manager): void
    {
        
$user = new User();
$user->setFirstname('saleh');
$user->setLastname('ben mohamed');
$user->setCin('06171809');
$user->setMobile('27099136');
$user->setBirthday(\DateTime::createFromFormat('Y-m-d', '2016-01-01'));
$user->setUniversity('sousse');
$user->setEmail('issatsousse@gmail.com');
$user->setRoles(['ROLE_ADMIN']);
$user->setPassword($this->passwordEncoder->encodePassword(
$user,
'000000'
));
$user1 = new User();
$user1->setFirstname('nour');
$user1->setLastname('essid');
$user1->setCin('06998716');
$user1->setMobile('26662643');
$user1->setBirthday(\DateTime::createFromFormat('Y-m-d', '2016-06-14'));
$user1->setUniversity('ISSAT');
$user1->setEmail('nouressid57@gmail.com');
$user1->setRoles(['ROLE_USER']);
$user1->setPassword($this->passwordEncoder->encodePassword(
$user1,
'06998716'
));
$manager->persist($user1);
$manager->persist($user);

$manager->flush();


    }
}
