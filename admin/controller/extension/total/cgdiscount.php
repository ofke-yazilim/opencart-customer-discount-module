<?php
class ControllerExtensionTotalCgdiscount extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/total/cgdiscount');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			$this->model_setting_setting->editSetting('total_cgdiscount', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/total/cgdiscount', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/total/cgdiscount', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=total', true);

		if (isset($this->request->post['total_cgdiscount_status'])) {
			$data['total_cgdiscount_status'] = $this->request->post['total_cgdiscount_status'];
		} else {
			$data['total_cgdiscount_status'] = $this->config->get('total_cgdiscount_status');
		}

		if (isset($this->request->post['total_cgdiscount_sort_order'])) {
			$data['total_cgdiscount_sort_order'] = $this->request->post['total_cgdiscount_sort_order'];
		} else {
			$data['total_cgdiscount_sort_order'] = $this->config->get('total_cgdiscount_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $this->load->model('customer/customer_group');
        $results = $this->model_customer_customer_group->getCustomerGroups();

        foreach ($results as $result) {

            if (isset($this->request->post['total_cgdiscount_group'.$result['customer_group_id']])) {
                $data['total_cgdiscount_group'.$result['customer_group_id']] = $this->request->post['total_cgdiscount_group'.$result['customer_group_id']];
            } else {
                $data['total_cgdiscount_group'.$result['customer_group_id']] = $this->config->get('total_cgdiscount_group'.$result['customer_group_id']);
            }

            $data['customer_groups'][] = array(
                'customer_group_id' => $result['customer_group_id'],
                'name'              => $result['name'],
                'value'             => $data['total_cgdiscount_group'.$result['customer_group_id']]
            );
        }

		$this->response->setOutput($this->load->view('extension/total/cgdiscount', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/total/cgdiscount')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}