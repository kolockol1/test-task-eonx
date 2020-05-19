<?php

namespace App\Database\Entities\MailChimp;

use Doctrine\ORM\Mapping as ORM;
use EoneoPay\Utils\Str;

/**
 * @ORM\Entity()
 */
class MailChimpMember extends MailChimpEntity
{
    /**
     * @ORM\Column(name="email_address", type="string")
     *
     * @var string
     */
    private $emailAddress;

    /**
     * @ORM\Column(name="email_type", type="string")
     *
     * @var string
     */
    private $emailType;

    /**
     * @ORM\Column(name="status", type="string")
     *
     * @var string
     */
    private $status;

    /**
     * @ORM\Column(name="merge_fields", type="array")
     *
     * @var array
     */
    private $mergeFields;

    /**
     * @ORM\Column(name="interests", type="array")
     *
     * @var array
     */
    private $interests;

    /**
     * @ORM\Column(name="language", type="string")
     *
     * @var string
     */
    private $language;

    /**
     * @ORM\Column(name="vip", type="boolean")
     *
     * @var bool
     */
    private $vip;

    /**
     * @ORM\Column(name="location", type="array")
     *
     * @var array
     */
    private $location;

    /**
     * @ORM\Column(name="marketing_permissions", type="array")
     *
     * @var array
     */
    private $marketingPermissions;

    /**
     * @ORM\Column(name="ip_signup", type="string")
     *
     * @var string
     */
    private $ipSignup;

    /**
     * @ORM\Column(name="timestamp_signup", type="string")
     *
     * @var string
     */
    private $timestampSignup;

    /**
     * @ORM\Column(name="ip_opt", type="string")
     *
     * @var string
     */
    private $ipOpt;

    /**
     * @ORM\Column(name="timestamp_opt", type="string")
     *
     * @var string
     */
    private $timestampOpt;

    /**
     * @ORM\Column(name="tags", type="array")
     *
     * @var array
     */
    private $tags;

    /**
     * @ORM\Id()
     * @ORM\Column(name="id", type="guid")
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    private $memberId;

    /**
     * @ORM\Column(name="mail_chimp_id", type="string", nullable=true)
     *
     * @var string
     */
    private $mailChimpId;

    /**
     * @inheritDoc
     */
    public function getValidationRules(): array
    {
        return [
            'email_address' => 'required|string',
            'email_type' => 'nullable|string|in:html,text',
            'status' => 'required|string|in:subscribed,unsubscribed,cleaned,pending,transactional',
            'merge_fields' => 'nullable|array',
            'interests' => 'nullable|array',
            'language' => 'nullable|string',
            'vip' => 'nullable|boolean',
            'location' => 'nullable|array',
            'location.latitude' => 'nullable|string', // todo change string to decimal|double
            'location.longitude' => 'nullable|string', // todo change string to decimal|double
            'marketing_permissions' => 'nullable|array',
            'marketing_permissions.marketing_permission_id' => 'nullable|string',
            'marketing_permissions.enabled' => 'nullable|boolean',
            'ip_signup' => 'nullable|string',
            'timestamp_signup' => 'nullable|string',
            'ip_opt' => 'nullable|string',
            'timestamp_opt' => 'nullable|string',
            'tags' => 'nullable|array',
        ];
    }

    /**
     * @inheritDoc
     */
    public function toArray(): array
    {
        $array = [];
        $str = new Str();

        foreach (\get_object_vars($this) as $property => $value) {
            $array[$str->snake($property)] = $value;
        }

        return $array;
    }

    /**
     * @return string
     */
    public function getMailChimpId(): string
    {
        return $this->mailChimpId;
    }

    /**
     * @param string $mailChimpId
     *
     * @return $this
     */
    public function setMailChimpId(string $mailChimpId): self
    {
        $this->mailChimpId = $mailChimpId;

        return $this;
    }

    /**
     * @param string $emailAddress
     *
     * @return $this
     */
    public function setEmailAddress(string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * @param string $emailType
     *
     * @return $this
     */
    public function setEmailType(string $emailType): self
    {
        $this->emailType = $emailType;

        return $this;
    }

    /**
     * @param string $status
     *
     * @return $this
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    /**
     * @param array $mergeFields
     *
     * @return $this
     */
    public function setMergeFields(array $mergeFields): self
    {
        $this->mergeFields = $mergeFields;

        return $this;
    }

    /**
     * @param array $interests
     *
     * @return $this
     */
    public function setInterests(array $interests): self
    {
        $this->interests = $interests;

        return $this;
    }

    /**
     * @param string $language
     *
     * @return $this
     */
    public function setLanguage(string $language): self
    {
        $this->language = $language;

        return $this;
    }

    /**
     * @param bool $vip
     *
     * @return $this
     */
    public function setVip(bool $vip): self
    {
        $this->vip = $vip;

        return $this;
    }

    /**
     * @param array $location
     *
     * @return $this
     */
    public function setLocation(array $location): self
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @param array $marketingPermissions
     *
     * @return $this
     */
    public function setMarketingPermissions(array $marketingPermissions): self
    {
        $this->marketingPermissions = $marketingPermissions;

        return $this;
    }

    /**
     * @param string $ipSignup
     *
     * @return $this
     */
    public function setIpSignup(string $ipSignup): self
    {
        $this->ipSignup = $ipSignup;

        return $this;
    }

    /**
     * @param string $timestampSignup
     *
     * @return $this
     */
    public function setTimestampSignup(string $timestampSignup): self
    {
        $this->timestampSignup = $timestampSignup;

        return $this;
    }

    /**
     * @param string $ipOpt
     *
     * @return $this
     */
    public function setIpOpt(string $ipOpt): self
    {
        $this->ipOpt = $ipOpt;

        return $this;
    }

    /**
     * @param string $timestampOpt
     *
     * @return $this
     */
    public function setTimestampOpt(string $timestampOpt): self
    {
        $this->timestampOpt = $timestampOpt;

        return $this;
    }

    /**
     * @param array $tags
     *
     * @return $this
     */
    public function setTags(array $tags): self
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->memberId;
    }
}