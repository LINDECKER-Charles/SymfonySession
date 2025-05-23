<?php

namespace App\Entity;

use App\Repository\InternRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InternRepository::class)]
class Intern
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    private ?string $interName = null;

    #[ORM\Column(length: 50)]
    private ?string $internSex = null;

    #[ORM\Column(length: 100)]
    private ?string $internCity = null;

    #[ORM\Column(length: 50)]
    private ?string $internCp = null;

    #[ORM\Column(length: 100)]
    private ?string $internAdress = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $internBirth = null;

    #[ORM\Column(length: 255)]
    private ?string $internEmail = null;

    /**
     * @var Collection<int, Session>
     */
    #[ORM\ManyToMany(targetEntity: Session::class, inversedBy: 'interns')]
    private Collection $sessions;

    public function __construct()
    {
        $this->sessions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInterName(): ?string
    {
        return $this->interName;
    }

    public function setInterName(string $interName): static
    {
        $this->interName = $interName;

        return $this;
    }

    public function getInternSex(): ?string
    {
        return $this->internSex;
    }

    public function setInternSex(string $internSex): static
    {
        $this->internSex = $internSex;

        return $this;
    }

    public function getInternCity(): ?string
    {
        return $this->internCity;
    }

    public function setInternCity(string $internCity): static
    {
        $this->internCity = $internCity;

        return $this;
    }

    public function getInternCp(): ?string
    {
        return $this->internCp;
    }

    public function setInternCp(string $internCp): static
    {
        $this->internCp = $internCp;

        return $this;
    }

    public function getInternAdress(): ?string
    {
        return $this->internAdress;
    }

    public function setInternAdress(string $internAdress): static
    {
        $this->internAdress = $internAdress;

        return $this;
    }

    public function getInternBirth(): ?\DateTime
    {
        return $this->internBirth;
    }

    public function setInternBirth(\DateTime $internBirth): static
    {
        $this->internBirth = $internBirth;

        return $this;
    }

    public function getInternEmail(): ?string
    {
        return $this->internEmail;
    }

    public function setInternEmail(string $internEmail): static
    {
        $this->internEmail = $internEmail;

        return $this;
    }

    /**
     * @return Collection<int, Session>
     */
    public function getSessions(): Collection
    {
        return $this->sessions;
    }

    public function addSession(Session $session): static
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
        }

        return $this;
    }

    public function removeSession(Session $session): static
    {
        $this->sessions->removeElement($session);

        return $this;
    }
}
