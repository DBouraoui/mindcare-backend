<?php

namespace App\DataFixtures;

use App\Entity\Pro;
use App\Entity\SchedulesPro;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use PHPStan\Parallel\Schedule;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserProFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $userPasswordHasher, private readonly EntityManagerInterface $entityManager,
    ){}
    public function load(ObjectManager $manager): void
    {
        $now = new \DateTimeImmutable();

        // Données crédibles pour ~10 professionnels autour de la santé mentale
        $items = [
            [
                'firstname' => 'Claire',
                'lastname' => 'Martin',
                'email' => 'claire.martin.psy@gmail.com',
                'phone' => '+33 6 12 34 56 01',
                'city' => 'Paris',
                'price' => '70',
                'diplome' => 'Psychologue clinicienne',
                'title' => 'Psychologue clinicienne',
                'siren' => '812345678',
                'siret' => '81234567800011',
                'address' => '12 rue des Acacias',
                'description' => 'Psychologue clinicienne spécialisée en thérapies cognitivo-comportementales pour adultes et adolescents. Prise en charge de l\'anxiété, dépression et troubles du sommeil.'
            ],
            [
                'firstname' => 'Marc',
                'lastname' => 'Leroy',
                'email' => 'marc.leroy.therapeute@gmail.com',
                'phone' => '+33 6 12 34 56 02',
                'city' => 'Lyon',
                'price' => '60',
                'diplome' => 'Psychothérapeute',
                'title' => 'Psychothérapeute',
                'siren' => '793456789',
                'siret' => '79345678900024',
                'address' => '4 avenue des Tilleuls',
                'description' => 'Psychothérapeute d\'orientation humaniste, accompagne le développement personnel, gestion du stress et burn-out. Consultations individuelles et ateliers de groupe.'
            ],
            [
                'firstname' => 'Sophie',
                'lastname' => 'Durand',
                'email' => 'sophie.durand.neuro@gmail.com',
                'phone' => '+33 6 12 34 56 03',
                'city' => 'Toulouse',
                'price' => '85',
                'diplome' => 'Neuropsychologue',
                'title' => 'Neuropsychologue',
                'siren' => '745678901',
                'siret' => '74567890100033',
                'address' => '21 boulevard Victor Hugo',
                'description' => 'Neuropsychologue spécialisée dans l\'évaluation cognitive et la rééducation après traumatisme crânien ou AVC. Bilan complet et prise en charge personnalisée.'
            ],
            [
                'firstname' => 'Antoine',
                'lastname' => 'Moreau',
                'email' => 'antoine.moreau.psy@gmail.com',
                'phone' => '+33 6 12 34 56 04',
                'city' => 'Nantes',
                'price' => '90',
                'diplome' => 'Psychiatre',
                'title' => 'Psychiatre',
                'siren' => '702345612',
                'siret' => '70234561200045',
                'address' => '5 place de la République',
                'description' => 'Psychiatre proposant consultations médicales, bilans et prescriptions si nécessaire. Expertise en troubles de l\'humeur et consultations spécialisées pour adolescents.'
            ],
            [
                'firstname' => 'Elodie',
                'lastname' => 'Petit',
                'email' => 'elodie.petit.sophro@gmail.com',
                'phone' => '+33 6 12 34 56 05',
                'city' => 'Bordeaux',
                'price' => '55',
                'diplome' => 'Sophrologue',
                'title' => 'Sophrologue',
                'siren' => '834567219',
                'siret' => '83456721900056',
                'address' => '9 impasse du Soleil',
                'description' => 'Sophrologue spécialisée en gestion du stress, préparation aux examens et techniques de relaxation. Séances individuelles et en entreprise.'
            ],
            [
                'firstname' => 'Julien',
                'lastname' => 'Garnier',
                'email' => 'julien.garnier.coach@gmail.com',
                'phone' => '+33 6 12 34 56 06',
                'city' => 'Strasbourg',
                'price' => '65',
                'diplome' => 'Coach en santé mentale',
                'title' => 'Coach en santé mentale',
                'siren' => '821234567',
                'siret' => '82123456700067',
                'address' => '3 rue du Faubourg',
                'description' => 'Coach certifié accompagnant les personnes en transition professionnelle, gestion du stress et amélioration du bien-être quotidien. Approche pratique et orientée objectifs.'
            ],
            [
                'firstname' => 'Anaïs',
                'lastname' => 'Faure',
                'email' => 'anais.faure.arttherapie@gmail.com',
                'phone' => '+33 6 12 34 56 07',
                'city' => 'Marseille',
                'price' => '60',
                'diplome' => 'Art-thérapeute',
                'title' => 'Art-thérapeute',
                'siren' => '799001234',
                'siret' => '79900123400078',
                'address' => '47 rue Sainte',
                'description' => 'Art-thérapeute utilisant la création artistique pour soutenir le travail psychique, l\'expression émotionnelle et la réparation de blessures psychiques.'
            ],
            [
                'firstname' => 'Nora',
                'lastname' => 'Rivière',
                'email' => 'nora.riviere.ergo@gmail.com',
                'phone' => '+33 6 12 34 56 08',
                'city' => 'Lille',
                'price' => '58',
                'diplome' => 'Ergothérapeute',
                'title' => 'Ergothérapeute spécialisée en santé mentale',
                'siren' => '755001122',
                'siret' => '75500112200089',
                'address' => '2 rue Gambetta',
                'description' => 'Ergothérapeute intervenant auprès de personnes en difficultés psychiques pour restaurer l\'autonomie dans les activités quotidiennes.'
            ],
            [
                'firstname' => 'Pauline',
                'lastname' => 'Bernard',
                'email' => 'pauline.bernard.infirmpsy@gmail.com',
                'phone' => '+33 6 12 34 56 09',
                'city' => 'Nice',
                'price' => '50',
                'diplome' => 'Infirmière en psychiatrie',
                'title' => 'Infirmière en psychiatrie',
                'siren' => '768901235',
                'siret' => '76890123500090',
                'address' => '88 avenue des Fleurs',
                'description' => 'Infirmière diplômée d\'État travaillant en consultation ambulatoire et accompagnement des patients atteints de troubles psychiques. Soins relationnels et soutien thérapeutique.'
            ],
        ];

        foreach ($items as $i => $data) {
            $user = new User();
            $user->setEmail($data['email'])
                ->setPassword($this->userPasswordHasher->hashPassword($user, 'password'))
                ->setFirstname($data['firstname'])
                ->setLastname($data['lastname'])
                ->setPhone($data['phone'])
                ->setCity($data['city'])
                ->setCreatedAt($now)
                ->setUpdatedAt($now)
                ->setRoles(['ROLE_PRO', 'ROLE_USER'])
                ->setIsActive(true);

            $manager->persist($user);

            $pro = new Pro();
            $pro->setPrice($data['price'])
                ->setCountry('France')
                ->setDescription($data['description'])
                ->setDiplome($data['diplome'])
                ->setTitle($data['title'])
                ->setSiren($data['siren'])
                ->setSiret($data['siret'])
                ->setAddress($data['address'])
                ->setCity($data['city'])
                ->setEmail($data['email'])
                ->setPhone($data['phone'])
                ->setUpdatedAt($now)
                ->setCreatedAt($now)
                ->setUtilisateur($user);

            $manager->persist($pro);

            $days = [
                'lundi',
                'mardi',
                'mercredi',
                'jeudi',
                'vendredi',
                'samedi',
                'dimanche',
            ];

            foreach ($days as $day) {
            $schedule = new SchedulesPro();
            $schedule->setPro($pro);
            $schedule->setDay($day);
            $schedule->setMorningStart(null);
            $schedule->setMorningEnd(null);
            $schedule->setAfternoonStart(null);
            $schedule->setAfternoonEnd(null);
            $schedule->setClosed(true);

            $manager->persist($schedule);
            }



            // Si votre entité User possède une relation setPro
            if (method_exists($user, 'setPro')) {
                $user->setPro($pro);
                $manager->persist($user);
            }

            // Optionnel : ajouter une référence si besoin pour d'autres fixtures
            $this->addReference('pro_' . $i, $pro);
        }

        $manager->flush();
    }
}
