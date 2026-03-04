<?php


use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserValidationTest extends KernelTestCase
{
    private ValidatorInterface $validator;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->validator = self::getContainer()->get("validator");
    }

    public function test_nom_trop_court(): void
    {
        $user = (new User())->setNom("Du");

        $errors = $this->validator->validate($user, null, ['length']);

        $this->assertCount(1, $errors, 'Erreur inattendue : ' . (string) $errors);
        $this->assertEquals("nom", $errors[0]->getPropertyPath());
        $this->assertStringContainsString("au moins 3 caractères", $errors[0]->getMessage());
    }

    public function test_nom_valide(): void
    {
        $user = new User();
        $user->setNom("Dupont");

        $errors = $this->validator->validate($user, null, ['length']);

        $this->assertCount(0, $errors, 'Erreur inattendue : ' . (string) $errors);

    }

    public function test_prenom_trop_court(): void
    {
        $user = (new User())->setPrenom("Fl");

        $errors = $this->validator->validate($user, null, ['length']);

        $this->assertCount(1, $errors, 'Erreur inattendue : ' . (string) $errors);
        $this->assertEquals("prenom", $errors[0]->getPropertyPath());
        $this->assertStringContainsString("au moins 3 caractères", $errors[0]->getMessage());
    }

    public function test_prenom_valide(): void
    {
        $user = (new User())->setPrenom("Florent");

        $errors = $this->validator->validate($user, null, ['length']);

        $this->assertCount(0, $errors, 'Erreur inattendue : ' . (string) $errors);

    }
    public function test_username_trop_court(): void
    {
        $user = (new User())->setUsername("Fl");

        $errors = $this->validator->validate($user, null, ['length']);

        $this->assertCount(1, $errors, 'Erreur inattendue : ' . (string) $errors);
        $this->assertEquals("username", $errors[0]->getPropertyPath());
        $this->assertStringContainsString("au moins 3 caractères", $errors[0]->getMessage());
    }

    public function test_username_valide(): void
    {
        $user = (new User())->setUsername("Florent44");

        $errors = $this->validator->validate($user, null, ['length']);

        $this->assertCount(0, $errors, 'Erreur inattendue : ' . (string) $errors);

    }



    public function test_email_mauvais_format(): void
    {
        $user = (new User())->setMail("Fl");

        $errors = $this->validator->validateProperty($user, 'mail');

        $this->assertGreaterThan(0, count($errors), "Le mail 'mauvais-format' devrait être invalide.");
        $this->assertStringContainsString('Votre email n\'est pas valide', $errors[0]->getMessage());
    }

    public function test_email_format_valide(): void
    {
        $user = (new User())->setMail("user@email.com");

        $errors = $this->validator->validateProperty($user, 'mail');

        $this->assertCount(0, $errors, 'Erreur inattendue : ' . (string) $errors);

    }

    public function test_password_encoder_fonctionnel(): void
    {
        $userPasswordHasher = self::getContainer()->get(UserPasswordHasherInterface::class);
        $user = new User();
        $plainPassword ='1234';
        $user->setPassword($userPasswordHasher->hashPassword($user,$plainPassword));

        // vérifier que le mot de passe est encodé
        $this->assertNotEquals($plainPassword, $user->getPassword());

        // vérifier que l'on arrive à le décodé
        $this->assertTrue($userPasswordHasher->isPasswordValid($user, $plainPassword));

    }

}
