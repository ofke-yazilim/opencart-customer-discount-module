<?php
class ModelExtensionTotalCgdiscount extends Model {
	public function getTotal($total) {
        $this->load->language('extension/total/cgdiscount');
        $discount_total = 0;
        if ($this->customer->isLogged()) {
            $group_id = $this->customer->getGroupId();
            if($group_id){
                $discount_rate = $this->config->get('total_cgdiscount_group'.$group_id);
                foreach ($this->cart->getProducts() as $product) {
                    $discount = 0;

                    $discount = ($discount_rate * $product['total'])/100;

                    if ($product['tax_class_id']) {
                        $tax_rates = $this->tax->getRates($product['total'] - ($product['total'] - $discount), $product['tax_class_id']);

                        foreach ($tax_rates as $tax_rate) {
                            if ($tax_rate['type'] == 'P') {
                                $total['taxes'][$tax_rate['tax_rate_id']] -= $tax_rate['amount'];
                            }
                        }
                    }

                    $discount_total += $discount;
                }

                $total['totals'][] = array(
                    'code'       => 'cgdiscount',
                    'title'      => str_replace('--','%',sprintf($this->language->get('text_cgdiscount'), $discount_rate)),
                    'value'      => -$discount_total,
                    'sort_order' => $this->config->get('total_cgdiscount_sort_order')
                );

                $total['total'] -= $discount_total;
            }
        }
	}
}
