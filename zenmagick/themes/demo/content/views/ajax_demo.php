<?php
/*
 * ZenMagick - Extensions for zen-cart
 * Copyright (C) 2006,2007 ZenMagick
 *
 * Portions Copyright (c) 2003 The zen-cart developers
 * Portions Copyright (c) 2003 osCommerce
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or (at
 * your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * $Id$
 */
?>

<p>This is a demo page illustrating Ajax in <em>ZenMagick</em>. The examples use <a href="http://www.json.org">JSON</a>
data format. If you want to use anything else, like XML, just write your own methods and you are good to go.</p>
<p>The controller is using <a href="http://pear.php.net/pepr/pepr-proposal-show.php?id=198">PEAR Json</a> for the JSON encoding.
Depending on your server configuration you might be better of using something different (which might already be installed.</p>

<p>The actual Ajax bits are implemented using <a href="http://www.jquery.com/">jQuery</a> and <a href="http://www.json.org/">json</a>.</p>

<p>Some things to keep in mind:</p>
<ul>
  <li>This is demo code and kept very simple</li>
  <li>The zones will be loaded when selecting a country (Austria and Canada are at the top of countries that have zones configured...)</li>
  <li>The JSON generating code in the Ajax controller is probably not the best (yet)</li>
  <li>The HTML formatting of the results is intentionally *very* simple</li>
  <li>There is a lot more that could be implemented as Ajax controller; reviews, etc...</li>
</ul>


<script type="text/javascript" src="<?php $zm_theme->themeURL("jquery/jquery.js") ?>"></script>
<script type="text/javascript" src="<?php $zm_theme->themeURL("jquery/interface.js") ?>"></script>
<script type="text/javascript" src="<?php $zm_theme->themeURL("json.js") ?>"></script>


<label for="msgbox"><strong>Messages</strong></label>
<div id="msgbox" style="height:1.8em;border:1px solid gray;margin:5px 0 12px;padding:3px;color:red"></div>
<form action="#">
    <fieldset>
        <legend>Shopping Cart</legend>
        <div id="cart" style="margin:4px 0;">
            Cart is empty.
        </div>
        <input type="button" value="Refresh cart" onclick="refreshCart();" />
    </fieldset>
</form>
<script type="text/javascript">
    var msgboxElem = document.getElementById('msgbox');
    var cartElem = document.getElementById('cart');

    // update cart content
    function updateCartContent(msg) {
        var json = eval('(' + msg + ')');
        for (var ii=0; ii < json.items.length; ++ii) {
            var item = json.items[ii];
            cartElem.innerHTML += "Id: "+item.id+", Name: "+item.name + ', qty: ' + item.qty + ', line total: ' + item.itemTotal + '<br>';
        }
        cartElem.innerHTML += '# of items in cart: ' + json.items.length + '<br>';
        cartElem.innerHTML += 'Total: ' + json.total + '<br>';
    }

    // refresh cart
    function refreshCart() {
        msgboxElem.innerHTML = "Refreshing cart ... ";
        cartElem.innerHTML = '';

        $.ajax({
            type: "POST",
            url: "<?php zm_ajax_href('shopping_cart', 'getContents') ?>",
            success: function(msg) {
                msgboxElem.innerHTML += "got response ... ";
                updateCartContent(msg);
                msgboxElem.innerHTML += "done!";
            }
        });
    }
</script>


<form action="#">
    <fieldset>
        <legend>Simple Ajax Shopping Cart</legend>
        <p>
            <label for="productId">ProductId</label>
            <input type="text" id="productId" name="productId" value="3" /><br />
            <label for="quantity">Quantity</label>
            <input type="text" id="quantity" name="quantity" value="1" /> (add/update)<br />
            <input type="button" value="Add to cart" onclick="sc_add();" />
            <input type="button" value="Remove from cart" onclick="sc_remove();" />
            <input type="button" value="Update quantity" onclick="sc_update();" />
        </p>
    </fieldset>
</form>


<script type="text/javascript">
    var productIdElem = document.getElementById('productId');
    var quantityElem = document.getElementById('quantity');

    function sc_add() {
        var productId = productIdElem.value;
        var quantity = quantityElem.value;

        msgboxElem.innerHTML = "Adding product " + productId + " ... ";
        cartElem.innerHTML = '';

        $.ajax({
            type: "POST",
            url: "<?php zm_ajax_href('shopping_cart', 'addProduct') ?>",
            data: "productId="+productId+"&quantity="+quantity,
            success: function(msg) {
                msgboxElem.innerHTML += "got response ... ";
                updateCartContent(msg);
                msgboxElem.innerHTML += "done!";
            }
        });
    }

    function sc_remove() {
        var productId = productIdElem.value;

        msgboxElem.innerHTML = "Removing product " + productId + " ... ";
        cartElem.innerHTML = '';

        $.ajax({
            type: "POST",
            url: "<?php zm_ajax_href('shopping_cart', 'removeProduct') ?>",
            data: "productId="+productId,
            success: function(msg) {
                msgboxElem.innerHTML += "got response ... ";
                updateCartContent(msg);
                msgboxElem.innerHTML += "done!";
            }
        });
    }

    function sc_update() {
        var productId = productIdElem.value;
        var quantity = quantityElem.value;

        msgboxElem.innerHTML = "Updating product " + productId + " ... ";
        cartElem.innerHTML = '';

        $.ajax({
            type: "POST",
            url: "<?php zm_ajax_href('shopping_cart', 'updateProduct') ?>",
            data: "productId="+productId+"&quantity="+quantity,
            success: function(msg) {
                msgboxElem.innerHTML += "got response ... ";
                updateCartContent(msg);
                msgboxElem.innerHTML += "done!";
            }
        });
    }
</script>


<form action="#">
    <fieldset>
        <legend>Simple Shipping Estimator</legend>
        <div id="methodList" style="margin:4px 0;">
        </div>
        <div id="address" style="margin:4px 0;">
        </div>
        <input type="button" value="(Re-)Calculate shipping" onclick="calculateShipping();" />
    </fieldset>
</form>
<script type="text/javascript">
    var methodListElem = document.getElementById('methodList');
    var addressElem = document.getElementById('address');

    // update shipping method list
    function updateShippingInfoSuccess(msg) {
        msgboxElem.innerHTML += "got response ...";

        var info = msg.parseJSON();

        if (0 == info.methods.length) {
            msgboxElem.innerHTML += "no shipping available ... ";
            methodListElem.innerHTML = '<strong>No Shipping Available (cart empty??)</strong><br>';
        } else {
            methodListElem.innerHTML = '<strong>Available methods:</strong><br>';
        }

        for (var ii=0; ii < info.methods.length; ++ii) {
            var method = info.methods[ii];
            methodListElem.innerHTML += method.id + ' ' + method.name + ' ' + method.cost + '<br>';
        }

        if (undefined !== info.address) {
            addressElem.innerHTML = '<strong>Address:</strong> ';
            addressElem.innerHTML += info.address.firstName + ' ' + info.address.lastName;
        }

        msgboxElem.innerHTML += "DONE!";
    };

    function updateShippingInfoFailure(msg) {
        msgboxElem.innerHTML += " update shipping failed!";
    };

    // calculate shipping for current customer/cart
    function calculateShipping() {
        msgboxElem.innerHTML = "Call shipping estimator ... ";
        methodListElem.innerHTML = '';
        addressElem.innerHTML = '';
        $.ajax({
            type: "GET",
            url: "<?php zm_ajax_href('shopping_cart', 'estimateShipping') ?>",
            success: function(msg) { updateShippingInfoSuccess(msg); },
            error: function(msg) { updateShippingInfoFailure(msg); }
        });
    }
</script>


<form action="#">
    <fieldset>
        <legend>Country / Zone demo</legend>
        <p>
            <label for="countries">Countries</label>
            <select id="countries" name="countries" onchange="loadZones()">
                <option value=""> --- </option>
            </select>
            <input type="button" value="Load Countries" onclick="loadCountries();" />
        </p>
        <p>
            <label for="zones">Zones</label>
            <select id="zones" name="zones">
                <option value=""> --- </option>
            </select>
        </p>
    </fieldset>
</form>
<script type="text/javascript">
    var countriesElem = document.getElementById('countries');
    var zonesElem = document.getElementById('zones');

    // Load countries
    function loadCountries() {
        msgboxElem.innerHTML = "Loading countries ... ";

        $.ajax({
            type: "GET",
            url: "<?php zm_ajax_href('country', 'getCountryList') ?>",
            success: function(msg) {
                msgboxElem.innerHTML += "got response ...";

                var json = msg.parseJSON();

                msgboxElem.innerHTML += "updating ... ";

                countriesElem.length = 0;
                var country = new Option('-- Select Country --', '', false, false);
                countriesElem.options[countriesElem.length] = country;

                for (var ii=0; ii < json.length; ++ii) {
                    var country = new Option(json[ii].name+' ('+json[ii].id+')', json[ii].id, false, false);
                    countriesElem.options[countriesElem.length] = country;
                }

                msgboxElem.innerHTML += "done!";
            }
        });
    }

    // load zones
    function loadZones() {
        var countryId = countriesElem.value;
        msgboxElem.innerHTML = "Loading zones for countryId="+countryId+" ... ";

        $.ajax({
            type: "GET",
            url: "<?php zm_ajax_href('country', 'getZonesForCountryId') ?>",
            data: "countryId="+countryId,
            success: function(msg) {
                msgboxElem.innerHTML += "got response ...";

                var json = msg.parseJSON();

                msgboxElem.innerHTML += "updating ... ";

                zonesElem.length = 0;
                var zone = new Option('-- Select Zone --', '', false, false);
                zonesElem.options[zonesElem.length] = zone;

                // zones are stored under their id
                //if (undefined === json.length) json = Object.values(json) 
                for (var ii=0; ii < json.length; ++ii) {
                    var zone = new Option(json[ii].name+' ('+json[ii].id+')', json[ii].id, false, false);
                    zonesElem.options[zonesElem.length] = zone;
                }

                msgboxElem.innerHTML += "done!";
            }
        });
    }
</script>
