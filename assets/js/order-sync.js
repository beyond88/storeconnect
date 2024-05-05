new Vue({
    el: '#hub-order-list',
    // data: {
    //     orders: [],
    //     searchQuery: '',
    //     currentPage: 1,
    //     pageSizeOptions: [5, 10, 20],
    //     selectedPageSize: 5, 
    //     isPopupOpen: false,
    //     selectedOrder: {},
    //     orderStatuses: {
    //         'wc-pending': 'Pending',
    //         'wc-processing': 'Processing',
    //         'wc-completed': 'Completed',
    //         'wc-on-hold': 'On Hold',
    //         'wc-cancelled': 'Cancelled',
    //         'wc-refunded': 'Refunded',
    //         'wc-failed': 'Failed'
    //     },
    //     isLoading: false,
    //     baseURL: window.location.origin,

    // },
    // computed: {
    //     filteredOrders() {
    //         return this.orders.filter(order =>
    //             order.id.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
    //             order.customer_name.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
    //             order.email.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
    //             order.status.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
    //             order.order_date.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
    //             order.shipping_date.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
    //             order.customer_note.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
    //             order.order_notes.toLowerCase().includes(this.searchQuery.toLowerCase())
    //         );
    //     },
    //     totalPages() {
    //         return Math.ceil(this.filteredOrders.length / this.selectedPageSize);
    //     },
    //     paginatedOrders() {
    //         const startIndex = (this.currentPage - 1) * this.selectedPageSize;
    //         return this.filteredOrders.slice(startIndex, startIndex + this.selectedPageSize);
    //     }
    // },
    // methods: {
    //     nextPage() {
    //         if (this.currentPage < this.totalPages) {
    //             this.currentPage++;
    //         }
    //     },
    //     prevPage() {
    //         if (this.currentPage > 1) {
    //             this.currentPage--;
    //         }
    //     },
    //     search() {
    //         this.currentPage = 1;
    //     },
    //     openPopup(order) {
    //         const popup = document.querySelector('.hc-popup');
    //         if (popup) {
    //             popup.classList.add('show-popup');
    //         }
    //         this.isPopupOpen = true;
    //         this.selectedOrder = order;

    //     },
    //     closePopup() {
    //         this.isPopupOpen = false;
    //         const popup = document.querySelector('.hc-popup');
    //         if (popup) {
    //             popup.classList.remove('show-popup');
    //         }
    //     },
    //     updateOrderStatus() {
    //         console.log('Order status updated:', this.selectedOrder.status);
    //     },
    //     updateOrderNotes() {
    //         console.log('Order notes updated:', this.selectedOrder.order_notes);
    //     },

    //     async updateOrder() {
    //         if(this.selectedOrder.id !=='' && this.selectedOrder.status !==''){

    //             const updateButton = document.getElementById('hc-update-order');

    //             try {
    //                 updateButton.disabled = true;
    //                 updateButton.textContent = 'Updating...';

    //                 const response = await axios.put(`${this.baseURL}/wp-json/hubcentral/v1/order/update`, {
    //                     id: this.selectedOrder.id,
    //                     status: this.selectedOrder.status,
    //                     note: this.selectedOrder.order_notes,
    //                     hub_item_id: this.selectedOrder.hub_item_id,
    //                 }, {});
    //                 this.isLoading.value = false;
    //             } catch (error) {
    //                 this.isLoading.value = false;
    //                 console.error('Error updating order status and adding note:', error.response.data);
    //             }
    //             finally {
    //                 // Restore original button label and enable button
    //                 updateButton.disabled = false;
    //                 updateButton.textContent = 'Update';
    //             }
    //         }
    //     }
    // },
    // created() {
    //     this.orders = ordersData;
    // }
});