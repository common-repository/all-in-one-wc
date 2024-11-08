<h1>Proforma Invoice</h1>
<p>
<table class="pdf_invoice_heading_table">
<tbody>
	<tr><th>Proforma Invoice Nr.</th><td>[aiow_proforma_invoice_number]</td></tr>
	<tr><th>Proforma Invoice Date</th><td>[aiow_proforma_invoice_date]</td></tr>
	<tr><th>Order Nr.</th><td>[aiow_order_number]</td></tr>
</tbody>
</table>
</p>
<p>
<table class="pdf_invoice_seller_buyer_table">
<tbody>
	<tr><th>Seller</th><th>Buyer</th></tr>
	<tr><td>COMPANY NAME<br>COMPANY ADDRESS 1<br>COMPANY ADDRESS 2<br></td><td>[aiow_order_billing_address]</td></tr>
</tbody>
</table>
</p>
<p>
[aiow_order_items_table table_class="pdf_invoice_items_table"
	columns="item_number|item_name|item_quantity|line_total_tax_excl"
	columns_titles="|Product|Qty|Total"
	columns_styles="width:5%;|width:75%;|width:5%;|width:15%;text-align:right;"
	discount_as_item="Discount"]
<table class="pdf_invoice_totals_table">
<tbody>
	<tr><th>Total (excl. TAX)</th><td>[aiow_order_total_excl_tax]</td></tr>
	<tr><th>Taxes</th><td>[aiow_order_total_tax hide_if_zero="no"]</td></tr>
	<tr><th>Order Total</th><td>[aiow_order_total]</td></tr>
</tbody>
</table>
</p>