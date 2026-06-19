<?php 
 return [
     'ignore.routes' => [
        //user 
        'user.updateImage',
        'user.editPassword',
        'user.updatePassword',
         //cost centers
         'checkCode.cost_center',
         //chart of accounts
         'checkCode.Chart_of_accounts_group',
         'checkCode.Chart_of_accounts',
         //gruop inputs
         'checkCode.input_group',
         //inputs
         'checkCode.inputs',
         //suppliers
         'checkCode.supplier',
         'getCNPJ.supplier',
         'new.contacts',
         'store_contacts.contacts',
         'edit.contact',
         'update_contacts.contacts',
         'delete.contact',
         //product
         'checkCode.product',
         'getInput.product',
         'getProduct.product',
         'getProductInputs.product',
         //group products
         'checkCode.group.product',
         //account
         'checkCode.account',
         //payment methods
         'checkCode.payment_methods',
         //sale proposal
         'getCollection.salesproposal',
         //billing
         'billing.getPaymentMethods',
         'billing.getSaleProposalAndProducts',
         'billing.download',
         'create.installments',
         'billingParcels.store_installments',
         'billingParcels.update_installments',
         'delete.installments',
         //contracts recurrent
         'contractRecu.files_store',
         'contractRecu.files_list',
         'recurrents.file.download',
         //parcels contract recurrent
         'create.contract.recurrent.instament',
         'contract.recurrent.installments_store',
         'edit.contract.recurrent.instament',
         'contract.recurrent.installments_update',
         'create.contract.recurrent.instament.commitment',
         'contract.recurrent.installment_commitment_update',
         'contract.recurrent.previewParcelings',
         'delete.contract.recurrent.installments',
         'recurrents.installments.download',
         //contract specific purpose
         'contractspecificpurpose.files_store',
         'contractspecificpurpose.files_list',
         'specificPurpose.file.download',
         //contract specific purpose parcels
         'create.contract.specific_purpose.instament',
         'contract.specific_purpose.instament.installments_store',
         'edit.contract.specific_purpose.instament',
         'contract.specific_purpose.instament.installments_update',
         'create.contract.specific_purpose.instament.commitment',
         'create.contract.specific_purpose.instament.installment_commitment_update',
         'create.contract.specific_purpose.instament.previewParcelings',
         'delete.contract.specific_purpose.installments',
         'specific_purpose.installments.download'
     ]
 ];