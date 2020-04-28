<?php

namespace denis909\yii;

use yii\db\ActiveRecord;

class DomainBehavior extends \yii\base\Behavior
{

    public $attributes = [];

    public $enableValidation = true;

    public $enableBeforeValidate = true;

    public $enableBeforeSave = true;

    public $suffix = '_domain';

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave'
        ];
    }

    protected function _setAttributes()
    {
        foreach($this->attributes as $attribute)
        {
            $url = parse_url($this->owner->{$attribute}, PHP_URL_HOST);

            if ($this->enableValidation && ($url === false))
            {
                $error = Yii::t(
                    'yii', 
                    '{attribute} is not a valid URL.', 
                    [
                        'attribute' => $this->owner->getAttributeLabel($this->owner->{$attribute})
                    ]
                );

                $this->owner->addError($this->owner->{$attribute}, $error);

                $url = null;
            }

            $this->owner->{$attribute . $this->suffix} = $url;
        }
    }

    public function beforeValidate($event)
    {
        if ($this->enableBeforeValidate)
        {
            $this->_setAttributes();
        }
    }

    public function beforeSave($event)
    {
        if ($this->enableBeforeSave)
        {
            $this->_setAttributes();
        }
    }

}