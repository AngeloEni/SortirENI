<?php

namespace App\Form\Model;

class EventFilterModel
{
    private $campus;
    private string $name;
    private $dateStart;
    private $myOrganisedEvents;
    private $myEvents;
    private $otherEvents;
    private $pastEvents;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return EventFilterModel
     */
    public function setName(string $name): EventFilterModel
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param mixed $campus
     * @return EventFilterModel
     */
    public function setCampus($campus)
    {
        $this->campus = $campus;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * @param mixed $dateStart
     * @return EventFilterModel
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMyOrganisedEvents()
    {
        return $this->myOrganisedEvents;
    }

    /**
     * @param mixed $myOrganisedEvents
     * @return EventFilterModel
     */
    public function setMyOrganisedEvents($myOrganisedEvents)
    {
        $this->myOrganisedEvents = $myOrganisedEvents;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMyEvents()
    {
        return $this->myEvents;
    }

    /**
     * @param mixed $myEvents
     * @return EventFilterModel
     */
    public function setMyEvents($myEvents)
    {
        $this->myEvents = $myEvents;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getOtherEvents()
    {
        return $this->otherEvents;
    }

    /**
     * @param mixed $otherEvents
     * @return EventFilterModel
     */
    public function setOtherEvents($otherEvents)
    {
        $this->otherEvents = $otherEvents;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPastEvents()
    {
        return $this->pastEvents;
    }

    /**
     * @param mixed $pastEvents
     * @return EventFilterModel
     */
    public function setPastEvents($pastEvents)
    {
        $this->pastEvents = $pastEvents;
        return $this;
    }



}