<?php

namespace Power;

interface iPAUserHand{
    public static function afterFetch($obj);
    public static function beforeSave($obj);
    public static function afterSave($obj);
    public static function afterDelete($obj);
    public static function beforeDelete($obj);
}