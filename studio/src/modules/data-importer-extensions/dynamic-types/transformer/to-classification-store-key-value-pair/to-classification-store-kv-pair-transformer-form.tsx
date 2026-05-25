import React from 'react'
import { Form, Input } from '@pimcore/studio-ui-bundle/components'
import { TransformerSettingsLayout } from '../transformer-settings-layout'

// TODO: Replace Input with an async Select that loads available ClassificationStore definitions
// from the Pimcore API (GET /api/pimcore-studio/v1/classes/classification-store-definitions or similar).

interface ToClassificationStoreKvPairTransformerConfig {
    storeId?: string | number | null
}

interface ToClassificationStoreKvPairTransformerFormProps {
    settings: ToClassificationStoreKvPairTransformerConfig
    onChange: (settings: ToClassificationStoreKvPairTransformerConfig) => void
}

export const ToClassificationStoreKvPairTransformerForm = ({ settings, onChange }: ToClassificationStoreKvPairTransformerFormProps): React.JSX.Element => (
    <TransformerSettingsLayout>
        <Form.Item
            extra="TODO: replace with async Classification Store picker"
            label="Classification Store ID"
        >
            <Input
                onChange={ (e) => { onChange({ ...settings, storeId: e.target.value || null }) } }
                placeholder="Store ID"
                value={ settings.storeId != null ? String(settings.storeId) : '' }
            />
        </Form.Item>
    </TransformerSettingsLayout>
)
