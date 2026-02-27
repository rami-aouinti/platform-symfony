<?php

declare(strict_types=1);

namespace App\Calendar\Domain\Entity;

use App\General\Domain\Entity\Interfaces\EntityInterface;
use App\General\Domain\Entity\Traits\Timestampable;
use App\General\Domain\Entity\Traits\Uuid;
use App\User\Domain\Entity\User;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidBinaryOrderedTimeType;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'calendar_event')]
#[ORM\Index(name: 'idx_calendar_event_start_at', columns: ['start_at'])]
#[ORM\Index(name: 'idx_calendar_event_end_at', columns: ['end_at'])]
#[ORM\Index(name: 'idx_calendar_event_status', columns: ['status'])]
#[ORM\Index(name: 'idx_calendar_event_visibility', columns: ['visibility'])]
#[ORM\Index(name: 'idx_calendar_event_user_id', columns: ['user_id'])]
#[ORM\ChangeTrackingPolicy('DEFERRED_EXPLICIT')]
class Event implements EntityInterface
{
    use Timestampable;
    use Uuid;

    #[ORM\Id]
    #[ORM\Column(name: 'id', type: UuidBinaryOrderedTimeType::NAME, unique: true, nullable: false)]
    #[Groups(['Event', 'Event.id', 'Event.show', 'Event.edit'])]
    private UuidInterface $id;

    #[ORM\Column(name: 'title', type: Types::STRING, length: 255, nullable: false)]
    #[Groups(['Event', 'Event.title', 'Event.create', 'Event.show', 'Event.edit'])]
    private string $title = '';

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    #[Groups(['Event', 'Event.user', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?User $user = null;

    #[ORM\Column(name: 'description', type: Types::TEXT, nullable: true)]
    #[Groups(['Event', 'Event.description', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?string $description = null;

    #[ORM\Column(name: 'location', type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['Event', 'Event.location', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?string $location = null;

    #[ORM\Column(name: 'start_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Groups(['Event', 'Event.startAt', 'Event.create', 'Event.show', 'Event.edit'])]
    private DateTimeImmutable $startAt;

    #[ORM\Column(name: 'end_at', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    #[Groups(['Event', 'Event.endAt', 'Event.create', 'Event.show', 'Event.edit'])]
    private DateTimeImmutable $endAt;

    #[ORM\Column(name: 'is_all_day', type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups(['Event', 'Event.isAllDay', 'Event.create', 'Event.show', 'Event.edit'])]
    private bool $isAllDay = false;

    #[ORM\Column(name: 'timezone', type: Types::STRING, length: 64, nullable: true)]
    #[Groups(['Event', 'Event.timezone', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?string $timezone = null;

    #[ORM\Column(name: 'status', type: Types::STRING, length: 64, nullable: false, options: ['default' => 'confirmed'])]
    #[Groups(['Event', 'Event.status', 'Event.create', 'Event.show', 'Event.edit'])]
    private string $status = 'confirmed';

    #[ORM\Column(name: 'visibility', type: Types::STRING, length: 32, nullable: false, options: ['default' => 'public'])]
    #[Groups(['Event', 'Event.visibility', 'Event.create', 'Event.show', 'Event.edit'])]
    private string $visibility = 'public';

    #[ORM\Column(name: 'is_cancelled', type: Types::BOOLEAN, options: ['default' => false])]
    #[Groups(['Event', 'Event.isCancelled', 'Event.create', 'Event.show', 'Event.edit'])]
    private bool $isCancelled = false;

    #[ORM\Column(name: 'url', type: Types::STRING, length: 2048, nullable: true)]
    #[Groups(['Event', 'Event.url', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?string $url = null;

    #[ORM\Column(name: 'color', type: Types::STRING, length: 32, nullable: true)]
    #[Groups(['Event', 'Event.color', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?string $color = null;

    #[ORM\Column(name: 'background_color', type: Types::STRING, length: 32, nullable: true)]
    #[Groups(['Event', 'Event.backgroundColor', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?string $backgroundColor = null;

    #[ORM\Column(name: 'border_color', type: Types::STRING, length: 32, nullable: true)]
    #[Groups(['Event', 'Event.borderColor', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?string $borderColor = null;

    #[ORM\Column(name: 'text_color', type: Types::STRING, length: 32, nullable: true)]
    #[Groups(['Event', 'Event.textColor', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?string $textColor = null;

    #[ORM\Column(name: 'organizer_name', type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['Event', 'Event.organizerName', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?string $organizerName = null;

    #[ORM\Column(name: 'organizer_email', type: Types::STRING, length: 255, nullable: true)]
    #[Groups(['Event', 'Event.organizerEmail', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?string $organizerEmail = null;

    #[ORM\Column(name: 'attendees', type: Types::JSON, nullable: true)]
    #[Groups(['Event', 'Event.attendees', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?array $attendees = null;

    #[ORM\Column(name: 'rrule', type: Types::TEXT, nullable: true)]
    #[Groups(['Event', 'Event.rrule', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?string $rrule = null;

    #[ORM\Column(name: 'recurrence_exceptions', type: Types::JSON, nullable: true)]
    #[Groups(['Event', 'Event.recurrenceExceptions', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?array $recurrenceExceptions = null;

    #[ORM\Column(name: 'recurrence_end_at', type: Types::DATETIME_IMMUTABLE, nullable: true)]
    #[Groups(['Event', 'Event.recurrenceEndAt', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?DateTimeImmutable $recurrenceEndAt = null;

    #[ORM\Column(name: 'recurrence_count', type: Types::INTEGER, nullable: true)]
    #[Groups(['Event', 'Event.recurrenceCount', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?int $recurrenceCount = null;

    #[ORM\Column(name: 'reminders', type: Types::JSON, nullable: true)]
    #[Groups(['Event', 'Event.reminders', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?array $reminders = null;

    #[ORM\Column(name: 'metadata', type: Types::JSON, nullable: true)]
    #[Groups(['Event', 'Event.metadata', 'Event.create', 'Event.show', 'Event.edit'])]
    private ?array $metadata = null;

    public function __construct()
    {
        $this->id = $this->createUuid();
        $this->startAt = new DateTimeImmutable();
        $this->endAt = $this->startAt;
    }

    public function getId(): string
    {
        return $this->id->toString();
    }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): self { $this->title = $title; return $this; }
    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): self { $this->user = $user; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): self { $this->description = $description; return $this; }
    public function getLocation(): ?string { return $this->location; }
    public function setLocation(?string $location): self { $this->location = $location; return $this; }
    public function getStartAt(): DateTimeImmutable { return $this->startAt; }
    public function setStartAt(DateTimeImmutable $startAt): self { $this->startAt = $startAt; return $this; }
    public function getEndAt(): DateTimeImmutable { return $this->endAt; }
    public function setEndAt(DateTimeImmutable $endAt): self { $this->endAt = $endAt; return $this; }
    public function isAllDay(): bool { return $this->isAllDay; }
    public function setIsAllDay(bool $isAllDay): self { $this->isAllDay = $isAllDay; return $this; }
    public function getTimezone(): ?string { return $this->timezone; }
    public function setTimezone(?string $timezone): self { $this->timezone = $timezone; return $this; }
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }
    public function getVisibility(): string { return $this->visibility; }
    public function setVisibility(string $visibility): self { $this->visibility = $visibility; return $this; }
    public function isCancelled(): bool { return $this->isCancelled; }
    public function setIsCancelled(bool $isCancelled): self { $this->isCancelled = $isCancelled; return $this; }
    public function getUrl(): ?string { return $this->url; }
    public function setUrl(?string $url): self { $this->url = $url; return $this; }
    public function getColor(): ?string { return $this->color; }
    public function setColor(?string $color): self { $this->color = $color; return $this; }
    public function getBackgroundColor(): ?string { return $this->backgroundColor; }
    public function setBackgroundColor(?string $backgroundColor): self { $this->backgroundColor = $backgroundColor; return $this; }
    public function getBorderColor(): ?string { return $this->borderColor; }
    public function setBorderColor(?string $borderColor): self { $this->borderColor = $borderColor; return $this; }
    public function getTextColor(): ?string { return $this->textColor; }
    public function setTextColor(?string $textColor): self { $this->textColor = $textColor; return $this; }
    public function getOrganizerName(): ?string { return $this->organizerName; }
    public function setOrganizerName(?string $organizerName): self { $this->organizerName = $organizerName; return $this; }
    public function getOrganizerEmail(): ?string { return $this->organizerEmail; }
    public function setOrganizerEmail(?string $organizerEmail): self { $this->organizerEmail = $organizerEmail; return $this; }
    public function getAttendees(): ?array { return $this->attendees; }
    public function setAttendees(?array $attendees): self { $this->attendees = $attendees; return $this; }
    public function getRrule(): ?string { return $this->rrule; }
    public function setRrule(?string $rrule): self { $this->rrule = $rrule; return $this; }
    public function getRecurrenceExceptions(): ?array { return $this->recurrenceExceptions; }
    public function setRecurrenceExceptions(?array $recurrenceExceptions): self { $this->recurrenceExceptions = $recurrenceExceptions; return $this; }
    public function getRecurrenceEndAt(): ?DateTimeImmutable { return $this->recurrenceEndAt; }
    public function setRecurrenceEndAt(?DateTimeImmutable $recurrenceEndAt): self { $this->recurrenceEndAt = $recurrenceEndAt; return $this; }
    public function getRecurrenceCount(): ?int { return $this->recurrenceCount; }
    public function setRecurrenceCount(?int $recurrenceCount): self { $this->recurrenceCount = $recurrenceCount; return $this; }
    public function getReminders(): ?array { return $this->reminders; }
    public function setReminders(?array $reminders): self { $this->reminders = $reminders; return $this; }
    public function getMetadata(): ?array { return $this->metadata; }
    public function setMetadata(?array $metadata): self { $this->metadata = $metadata; return $this; }
}
