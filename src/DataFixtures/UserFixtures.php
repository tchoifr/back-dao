<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $usersData = [
            [
                'walletAddress' => '0xADMIN1234ABCD',
                'username' => 'AdminMaster',
                'roles' => ['admin'],
                'userToken' => 'token-admin-xyz',
                'network' => 'ETH',
                'solBalance' => '0.00000000',
                'ethBalance' => '25.00000000',
                'workBalance' => '1000.00000000',
            ],
            [
                'walletAddress' => '0xFREELANCE5678EFG',
                'username' => 'FreeLanceJoe',
                'roles' => ['freelance'],
                'userToken' => 'token-free-123',
                'network' => 'SOL',
                'solBalance' => '45.23000000',
                'ethBalance' => '0.00000000',
                'workBalance' => '300.00000000',
            ],
            [
                'walletAddress' => '0xEMPLOYEUR9876HIJ',
                'username' => 'BossMan',
                'roles' => ['employeur'],
                'userToken' => 'token-boss-abc',
                'network' => 'ETH',
                'solBalance' => '0.00000000',
                'ethBalance' => '50.12500000',
                'workBalance' => '750.00000000',
            ],
            [
                'walletAddress' => '0xDAO0011223344',
                'username' => 'DaoCentral',
                'roles' => ['dao'],
                'userToken' => 'token-dao-xyz',
                'network' => 'SOL',
                'solBalance' => '1000.00000000',
                'ethBalance' => '100.00000000',
                'workBalance' => '5000.00000000',
            ],
        ];

        foreach ($usersData as $data) {
            // ✅ Vérifie si l'utilisateur existe déjà
            $user = $manager->getRepository(User::class)->findOneBy([
                'walletAddress' => $data['walletAddress'],
            ]) ?? new User();

            $user->setWalletAddress($data['walletAddress']);
            $user->setUsername($data['username']);
            $user->setRoles($data['roles']); // ✅ tableau
            $user->setUserToken($data['userToken']);
            $user->setNetwork($data['network']);
            $user->setSolBalance($data['solBalance']);
            $user->setEthBalance($data['ethBalance']);
            $user->setWorkBalance($data['workBalance']);

            $manager->persist($user);
        }

        $manager->flush();
    }
}
