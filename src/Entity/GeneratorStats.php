<?php

namespace App\Entity;

use App\Repository\GeneratorStatsRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GeneratorStatsRepository::class)]
class GeneratorStats
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $generator_id;

    #[ORM\Column(type: 'float')]
    private $average_power;

    #[ORM\Column(type: 'integer')]
    private $hour;

    #[ORM\Column(type: 'date')]
    private $date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGeneratorId(): ?int
    {
        return $this->generator_id;
    }

    public function setGeneratorId(?int $generator_id): self
    {
        $this->generator_id = $generator_id;

        return $this;
    }

    public function getAveragePower(): ?float
    {
        return $this->average_power;
    }

    public function setAveragePower(float $average_power): self
    {
        $this->average_power = $average_power;

        return $this;
    }

    public function getHour(): ?int
    {
        return $this->hour;
    }

    public function setHour(int $hour): self
    {
        $this->hour = $hour;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getDateAsString(): string {
        return $this->date->format("Y-m-d");
    }
}
