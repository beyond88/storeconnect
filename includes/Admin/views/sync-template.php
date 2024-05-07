<div class="sync-order-area" id="sync-order-area">

    <!-- <div class="sync-status-area" id="sync-status-area"></div> -->

    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc" style="width: 30%">
                    <label>WooCommerce HubCentral</label>
                </th>
                <td class="forminp forminp-text" style="width: 10%;display: flex;align-items: center;">
                    <div class="lds-spinner" id="sync-spinner">
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                    <button id="storeconnect-order-sync-btn" type="button" class="button" @click="updateOrder">
                        Sync now
                    </button>
                </td>
                <td class="forminp forminp-text" style="width: 50%">
                    <button id="storeconnect-order-stop-sync-btn" type="button" class="button" @click="stopStatus">
                        Stop sync
                    </button>
                </td>
            </tr>
        </tbody>
    </table>
</div>