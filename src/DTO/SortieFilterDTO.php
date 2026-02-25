<?php

namespace App\DTO;


use App\Entity\Site;

class SortieFilterDTO
{
    //Le nom des propriétés correspond aux noms des champs du formulaire
    public ?string $inputSearch = null;
//    public ?array $sites = [];
    public ?Site $site = null;
    public ?\DateTimeInterface $dateMin = null;
    public ?\DateTimeInterface $dateMax = null;
    public ?bool $isOrganisateur = null;
    public ?bool $isInscrit = null;
    public ?bool $isNotInscrit = null;
    public ?bool $ended = null;
}
