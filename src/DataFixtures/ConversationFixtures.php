<?php

namespace App\DataFixtures;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Uid\Uuid;

class ConversationFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // 🧑‍💼 Récupération ou création d’un employeur
        $employer = $manager->getRepository(User::class)->findOneBy(['username' => 'BossMan']);
        if (!$employer) {
            $employer = new User();
            $employer->setWalletAddress('0xEMPLOYER_FIXTURE_' . strtoupper($faker->bothify('####')));
            $employer->setUsername('BossMan');
            $employer->setRoles(['employer']);
            $employer->setNetwork('ETH');
            $employer->setEthBalance('10.00');
            $manager->persist($employer);
        }

        // 👩‍💻 Récupération ou création d’un freelance
        $freelancer = $manager->getRepository(User::class)->findOneBy(['username' => 'FreeLanceJoe']);
        if (!$freelancer) {
            $freelancer = new User();
            $freelancer->setWalletAddress('0xFREELANCE_FIXTURE_' . strtoupper($faker->bothify('####')));
            $freelancer->setUsername('FreeLanceJoe');
            $freelancer->setRoles(['freelance']);
            $freelancer->setNetwork('SOL');
            $freelancer->setSolBalance('5.00');
            $manager->persist($freelancer);
        }

        $manager->flush(); // 👈 important avant de créer la conversation (id disponibles)

        // 💬 Création de la conversation
        $conversation = new Conversation();
        $uuid = Uuid::v4();
        $conversation->setUuid($uuid);
        $conversation->setEmployer($employer);
        $conversation->setFreelancer($freelancer);
        $conversation->setProject('Audit Smart Contract DAO');
        $conversation->setActive(true);

        // 🗨️ Quelques messages
        $messages = [
            ['from' => 'employer', 'text' => 'Bonjour, disponible pour discuter du projet ?'],
            ['from' => 'freelancer', 'text' => 'Oui bien sûr, je suis dispo maintenant.'],
            ['from' => 'employer', 'text' => 'Parfait ! J’aimerais un audit rapide de notre DAO.'],
        ];

        foreach ($messages as $m) {
            $msg = new Message();
            $msg->setConversation($conversation);
            $msg->setFrom($m['from']);
            $msg->setText($m['text']);
            $msg->setCreatedAt(new \DateTimeImmutable('-' . rand(1, 5) . ' hours'));
            $msg->setReadByEmployer($m['from'] === 'employer');
            $msg->setReadByFreelancer($m['from'] === 'freelancer');
            $manager->persist($msg);
        }

        $manager->persist($conversation);
        $manager->flush();

        echo "✅ Conversation fixture créée avec succès :\n";
        echo "   - UUID : {$uuid}\n";
        echo "   - Freelance : " . $freelancer->getUsername() . "\n";
        echo "   - Employer : " . $employer->getUsername() . "\n";
    }
}
