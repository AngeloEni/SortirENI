<?php

namespace App\Form\Model;

use App\Entity\Campus;
use phpDocumentor\Reflection\Types\Boolean;

class EventFilterModel
{
    private ?Campus $campus = null;
    private ?string $name = null;
    private ?\DateTime $earliestDate =null;
    private ?\DateTime $latestDate =null;
    private ?bool $myOrganisedEvents=null;
    private ?bool $myEvents =null;
    private ?bool $otherEvents =null;
    private ?bool $pastEvents =null;

    /**
     * @return Campus|null
     */
    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    /**
     * @param Campus|null $campus
     * @return EventFilterModel
     */
    public function setCampus(?Campus $campus): EventFilterModel
    {
        $this->campus = $campus;
        return $this;
    }


    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return EventFilterModel
     */
    public function setName(?string $name): EventFilterModel
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getEarliestDate(): ?\DateTime
    {
        return $this->earliestDate;
    }

    /**
     * @param \DateTime|null $earliestDate
     * @return EventFilterModel
     */
    public function setEarliestDate(?\DateTime $earliestDate): EventFilterModel
    {
        $this->earliestDate = $earliestDate;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getLatestDate(): ?\DateTime
    {
        return $this->latestDate;
    }

    /**
     * @param \DateTime|null $latestDate
     * @return EventFilterModel
     */
    public function setLatestDate(?\DateTime $latestDate): EventFilterModel
    {
        $this->latestDate = $latestDate;
        return $this;
    }


    /**
     * @return bool|null
     */
    public function getMyOrganisedEvents(): ?bool
    {
        return $this->myOrganisedEvents;
    }

    /**
     * @param bool|null $myOrganisedEvents
     * @return EventFilterModel
     */
    public function setMyOrganisedEvents(?bool $myOrganisedEvents): EventFilterModel
    {
        $this->myOrganisedEvents = $myOrganisedEvents;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getMyEvents(): ?bool
    {
        return $this->myEvents;
    }

    /**
     * @param bool|null $myEvents
     * @return EventFilterModel
     */
    public function setMyEvents(?bool $myEvents): EventFilterModel
    {
        $this->myEvents = $myEvents;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getOtherEvents(): ?bool
    {
        return $this->otherEvents;
    }

    /**
     * @param bool|null $otherEvents
     * @return EventFilterModel
     */
    public function setOtherEvents(?bool $otherEvents): EventFilterModel
    {
        $this->otherEvents = $otherEvents;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getPastEvents(): ?bool
    {
        return $this->pastEvents;
    }

    /**
     * @param bool|null $pastEvents
     * @return EventFilterModel
     */
    public function setPastEvents(?bool $pastEvents): EventFilterModel
    {
        $this->pastEvents = $pastEvents;
        return $this;
    }

}