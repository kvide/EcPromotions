<h3>What does this do?</h3>
<p>This module works in conjunction with the EcProductMgr and EcOrders module to allow creating and managing time limited promotions for the products on your e-commerce website.</p>

<h3>Features:</h3>
<ul>
<li><u>All promotions are time limited.</u><br/><br/></li>
<li><u>A promotion can have one or more conditions to indicate eligibility.</u>
  <p>If a promotion is defined with two or more conditions, all of those conditions must be met in order for the customer to be eligible for the promotion.</p>
  <p>A number of condition types exist... However some of them cannot be combined with others.  i.e:  You cannot have two different type of product conditions.</p>
  <ul>
<li><u>Require membership in an FEU Group:</u>
  <p>You can enter a comma separated list of FEU group names.  The logged in website visitor must be a member of one of these groups to be eligible for the promotion.</p></li>
  <li><u>Require a minimum purchase amount:</u>
  <p>In order for a customer to meet this condition, the pre-tax/pre-shipping total for their order must meet the specified minimum value.</p></li>
  <li><u>Require the purchase of a specific product:</u>
  <p>In order for a customer to meet this condition, his cart must contain at least one product matching the comma separated list of product ids entered.</p></li>
  <li><u>Require purchase of atleast one product in a category:</u>
  <p>In order for a customer to meet this condition, his cart must contain at least one product that is within at least one of the comma separated list of category names (from the products module) specified.</p></li>
  <li><u>Require purchase of atleast one product in a hierarchy:</u>
  <p>In order for a customer to meet this condition, his cart must contain at least one product that is within the specified products module hierarchy.</p>
  <p>Hierarchy names can be specified in the form of &quot;Parent.Parent.Child&quot; i.e: &quot;Clothing.Tents&quot;.  Hierarchy names are not case sensitive.  Wildcards can be specfified by using the * character.  i.e: &quot;Clothing*&quot;.</p></li>
  <li><u>Require a Coupon Code:</u>
  <p>In order for a customer to meet this condition, he must have supplied the coupon code that matches this condition.</p>
  <p>In order to allow customers to enter coupon codes, you need to add a tag into your page template or page content like {Promotions}.  This will display a form that allows your users to enter coupon codes.  Coupon codes are remembered until the user closes the browser.</p></li>
  <li><u>Require a minimum order weight:</u>
  <p>In order for a customer to meet this condition, the sum total of all of the weight values for the items in his cart must meet or exceed the value specified.</p></li>
  </ul>
</li>

<li><u>Each promotion can have a different &quot;offer&quot;</u>
<p>A number of different offers types are available:</p>
  <ul>
<li><u>Percentage off order total:</u>
<p>This offer type allows you to specify a floating point value between 0 and 1 to indicate a percentage off.  i.e: 0.1 indicates 10% off.   During the checkout process, any non tax, or non shipping item in the order will be reduced in price by the specified amount.</p></li>
<li><u>Reduce order total by a specified amount:</u>
<p>This offer type allows you to reduce the amount of the order by a certain dollar figure.  This dollar figure will be added as a discount item into the order line items and will appear on the invoice.</p></li>
<li><u>Receive a Free Product:</u>
<p>This offer type allows you to specify a product id to automatically include in the order at no cost.  This product will be added as a product item into the order line items, but will appear at no cost.</p></li>
<li><u>Discount the applicable products by a percentage:</u>
<p>This offer type only works if at least one product condition is specified.  If a product condition is speciried, and matches, this offer type will discount the matching products from the customer order by the specified value.</p>.
  </ul>
</li>
<li><u>Allows website visitors to enter one or more coupon codes</u><br/><br/></li>
<li><u>Discounts are calculated and applied to the order during the checkout process.</u><br/><br/></li>
<li><u>If a web customer is eligible for multiple promotions, the one with the greatest value is applied.</u></li>
</ul>
<br/>

<h3>How Do I Use It:</h3>
<ol>
  <li>1. Install and configure the module</li>
  <li>2. Create at least one promotion</li>
  <li>3. Specify this module as the &quot;Promotions&quot; module within the Orders Preferences.</li>
  <li>4. <em>(optional)</em> Add {Promotions} to a page template or page content.</em></li>
</ol>

<h3>Example:</h3>
<p>In this example we will create a promotion like &quot;10% off all orders over $100, with coupon code&quot;:</p>
<ul>
  <li>1. <u>Create a new promotion</u>
  <p>Navigate to the promotions module admin panel, and click on &quot;Add a New Promotion&quot;.  You will be presented with the promotions form.</p></li>
  <li>2. <u>Specify a promotion name</u>
  <p>For this example we will use the name &quot;Simple Sale&quot;</p></li>
  <li>3. <u>Add a coupon condition</u>
  <p>From the &quot;Type of Condition&quot; dropdown select &quot;Require a Coupon Code&quot;.  Enter &quot;simplesale&quot; into the input field below and click Add.</p></li>
  <li>4. <u>Add an order price condition</u>
  <p>From the &quot;Type of Condition&quot; dropdown select &quot;Require a Minimum Purchase Amount&quot; from the dropdown, and enter 100 into the input field below.  Click Add.</p></li>
  <li>5. <u>Specify the offer type, and amount</u>
  <p>Select the offer type &quot;Percentage off order total&quot; and enter 0.1 into the text field below to indicate 10%&quot;</p></li>
  <li>6. <u>Click submit</u></li>
</ul>

<h3>Smarty Plugins</h3>
<p>This module also provides a smarty plugin to aid in retrieving and displaying discounts for a single product.</p>
<ul>
  <li><strong>{promo_get_prod_discount [feu_uid=val] product_id=val [assign=var]}</strong>
  <p>If successful, this plugin returns a hash containing discount information for the specified product.  The greatest possible discount is calculated, considering all active promotions, user id, entered coupon codes, and other matching conditions.</p>
  <p>Parameters:</p>
  <ul>
  <li>feu_uid (optional) - The MAMS userid to test with.  If this is not specified, the currently logged in user id will be used.</li>
  <li>product_id (required) - The ID of the product to test with.</li>
  <li>assign (optional) - Optionally assign the output to the specified smarty variable.</li>
  </ul>
  </li>
</ul>

<h3>Cautions:</h3>
<p>1. Promotions are calculated on the order in sequence.  Multiple promotions may match the items of a single order, this may result in a single order having a zero <em style="color: red;">negative value</em>.  The orders module may do some basic checks to help with order integrity, but do not count on it.  You are encouraged to TEST your promotions and to always monitor them before rolling them live.</p>
<p>2. Instant promotions relying on coupon codes are not recommended.  This is because the contents of the cart (or multiple carts) are not analyzed after a user enters a coupon code, only when adding or removing cart items.</p>

<h3>Support</h3>
<p>The module author is in no way obligated to privide support for this code in any fashion.  However, there are a number of resources available to help you with it:</p>
<ul>
<li>A bug tracking and feature request system has been created for this module <a href="http://dev.cmsmadesimple.org/projects/promotions">here</a>.  Please be verbose and descriptive when submitting bug reports and feature requests, and for bug reports ensure that you have provided sufficient information to reliably reproduce the issue.</li>
<li>Additional discussion of this module may also be found in the <a href="http://forum.cmsmadesimple.org">CMS Made Simple Forums</a>.  When describing an issue please make an effort to privide all relavant information, a thorough description of your issue, and steps to reproduce it or your discussion may be ignored.</li>
<li>The author, calguy1000, can often be found in the <a href="irc://irc.freenode.net/#cms">CMS IRC Channel</a>.</li>
<li>Lastly, you may have some success emailing the author directly.  However, please use this as a last resort, and ensure that you have followed all applicable instructions on the forge, in the forums, etc.</li>
</ul>

<h3>Copyright and License</h3>
<p>Copyright &copy; 2023-2024, CMS Made Simple Foundation <a href="mailto:info@cmsmadesimple.org">&lt;info@cmsmadesimple.org&gt;</a>. All Rights Are Reserved.</p>
<p>Copyright &copy; 2009-2018, Robert Campbell <calguy1000>. All Rights Are Reserved.</p>
<p>This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.</p>
<p>However, as a special exception to the GPL, this software is distributed
as an addon module to CMS Made Simple.  You may not use this software
in any Non GPL version of CMS Made simple, or in any version of CMS
Made simple that does not indicate clearly and obviously in its admin
section that the site was built with CMS Made simple.</p>
<p>This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
Or read it <a href="http://www.gnu.org/licenses/licenses.html#GPL">online</a></p>

<hr>
<h4>Current Maintainers</h4>
<ul>
  <li class="p_bottom_15"><strong>Christian Kvikant</strong></li>
</ul>

<h4>Past Maintainers</h4>
<ul>
  <li><strong>Robert Campbell (calguy1000)</strong> <br /></li>
</ul>
