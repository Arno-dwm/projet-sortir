<?php

use App\Entity\Site;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
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

        $this->assertCount(1, $errors, 'une erreur attendue');
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

        $this->assertCount(1, $errors, 'une erreur attendue');
        $this->assertEquals("prenom", $errors[0]->getPropertyPath());
        $this->assertStringContainsString("au moins 3 caractères", $errors[0]->getMessage());
    }

    public function test_prenom_valide(): void
    {
        $user = (new User())->setPrenom("Florent");

        $errors = $this->validator->validate($user, null, ['length']);

        $this->assertCount(0, $errors, 'Erreur inattendue : ' . (string) $errors);

    }

}
