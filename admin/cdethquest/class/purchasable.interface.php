<?php
interface PurchasableItem {
  function getName();
  function getDescription();
  function getPrice();
  function getPremium();
  function getIcon();
  function getSellingprice();
}
?>