<?php

namespace App\DTO;

class SortieFilterDTO
{
    //Le nom des propriétés correspond aux noms des champs du formulaire
    public ?string $inputSearch = null;
    public ?\DateTimeInterface $dateMin = null;
    public ?\DateTimeInterface $dateMax = null;

    public ?bool $isOrganisateur = null;
    public ?bool $isInscrit = null;
    public ?bool $isNotInscrit = null;
    public ?bool $ended = null;
}
