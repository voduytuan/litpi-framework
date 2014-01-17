<?php

namespace Vendor\Litpi;

class Cookiecart
{
    protected $cartSession = '';
    protected $items = array();	//phan tu la {ID}_{ATTRIBUTE} de phan biet sp cung ID, nhung khac attribute
    protected $itemquantitys = array();
    protected $db;

    //constructor function
    public function __construct()
    {
        $this->retrieveFromSession();
    }

    private function initCart()
    {
        global $registry, $mobiledetect;

        $this->cartSession = Helper::getSessionId();
    }

    /**
     * Dung de lay du lieu trong database
     *
     */
    public function retrieveFromSession()
    {
        if (!empty($_COOKIE['cartSessionDetail'])) {
            $listcartdetail = unserialize($_COOKIE['cartSessionDetail']);
            if (!empty($listcartdetail)) {
                foreach ($listcartdetail as $cartdetail) {
                    $cartdetailexplode = explode(':', $cartdetail);
                    if (!empty($cartdetailexplode)) {
                        $this->addItem(
                            $cartdetailexplode[0],
                            $cartdetailexplode[1],
                            array(
                                'promotionid' => !empty($cartdetailexplode[2])?$cartdetailexplode[2]:'',
                                'regionid' => 3
                            )
                        );//mac dinh region la ho chi minh
                    }
                }
            }
        }
    }

    /**
     * Luu thong tin hien tai cua cart vao database
     *
     */
    public function saveToSession()
    {
        $items = $this->getContents();

        if (!empty($items)) {
            $arrayitemsaved = array();
            //insert new detail for current cart
            foreach ($items as $item) {
                $arrayitemsaved[] = $item->id.':'.$item->quantity.':'
                    . (!empty($item->options['promotionid'])?$item->options['promotionid']:'');
                // array('p_id' => $item->id, 'cd_quantity' => $item->quantity,
                // 'cd_attribute' => serialize($item->options));
            }
            setcookie('cartSessionDetail', serialize($arrayitemsaved), time() + 30*24*3600, '/');
            $_COOKIE['cartSessionDetail'] = serialize($arrayitemsaved);
        } else {
            unset($_COOKIE['cartSessionDetail']);
            setcookie('cartSessionDetail', '', time() - 30*24*3600, '/');
        }
    }

    public function getContents()
    {
        $items = array();

        foreach ($this->items as $tmp_item) {
            $item = new StdClass();
            $itemgroup = explode(':', $tmp_item, 2);

            $item->id = $itemgroup['0'];

            //check if this product have option (can be promotion, attribute...)
            if (count($itemgroup) > 0) {
                $item->options = unserialize($itemgroup['1']);
            }

            $item->quantity = $this->itemquantitys[$tmp_item];
            $items[] = $item;
        }

        return $items;
    }

    public function addItem($itemid, $quantity = 1, $options = array())
    { // adds an item to cart

        $itemgroup = $this->getItemGroup($itemid, $options);

        if (strlen($itemgroup) > 0) {
            if (!empty($this->itemquantitys[$itemgroup]) && $this->itemquantitys[$itemgroup] > 0) {
              // so we'll just increase the quantity
                $this->itemquantitys[$itemgroup] = $quantity + $this->itemquantitys[$itemgroup];
            } else {
                $this->items[]=$itemgroup;
                $this->itemquantitys[$itemgroup] = $quantity;
            }
        }
    }


    public function editItem($itemid, $quantity, $options = array())
    { // changes an items quantity

        $itemgroup = $this->getItemGroup($itemid, $options);

        if ($quantity < 1) {
            $this->delItem($itemgroup);
        } else {
            $this->itemquantitys[$itemgroup] = $quantity;
        }
    }


    public function getItemGroup($itemid, $options = array())
    {
        if (count($options) > 0) {
            $itemgroup = $itemid . ':' . serialize($options);
        } else {
            $itemgroup = $itemid;
        }

        return $itemgroup;
    }

    public function delItem($itemid, $options = array())
    {
        // removes an item from cart
        $itemgroup = $this->getItemGroup($itemid, $options);

        $ti = array();
        $this->itemquantitys[$itemgroup] = 0;
        foreach ($this->items as $item) {
            $nitem = explode(':', $item, 2);
            if (!empty($nitem[0]) && $nitem[0] != $itemgroup) {
                $ti[] = $item;
            }
        }
        $this->items = $ti;
    } //end of del_item

    public function emptyCart()
    { // empties / resets the cart
        $this->items = array();
        $this->itemquantitys = array();
    } // end of empty cart

    public function getCurrentQuantity($itemid, $options)
    {
        $itemgroup = $this->getItemGroup($itemid, $options);

        return $this->itemquantitys[$itemgroup];
    }

    public function itemCount()
    {
        return count($this->items);
    }
}
