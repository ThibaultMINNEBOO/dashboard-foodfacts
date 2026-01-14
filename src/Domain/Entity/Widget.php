<?php

namespace App\Domain\Entity;

use App\Domain\Enums\WidgetType;
use App\Infrastructure\Doctrine\Repository\WidgetRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WidgetRepository::class)]
class Widget
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private string $title;

    #[ORM\Column(enumType: WidgetType::class)]
    private ?WidgetType $type = null;

    #[ORM\Column]
    private ?int $position = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private ?array $configuration = [];

    #[ORM\ManyToOne(inversedBy: 'widgets')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Dashboard $dashboard = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public static function create(string $title, WidgetType $type, int $position, array $configuration = []): Widget
    {
        $widget = new self();
        $widget->title = $title;
        $widget->type = $type;
        $widget->position = $position;
        $widget->configuration = $configuration;

        return $widget;
    }

    public function withDashboard(Dashboard $dashboard): static
    {
        $this->dashboard = $dashboard;

        return $this;
    }

    public function getType(): ?WidgetType
    {
        return $this->type;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getConfiguration(): ?array
    {
        return $this->configuration;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return Dashboard|null
     */
    public function getDashboard(): ?Dashboard
    {
        return $this->dashboard;
    }
}
