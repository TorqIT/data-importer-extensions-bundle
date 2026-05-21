import React from 'react'
import { Form, Input } from '@pimcore/studio-ui-bundle/components'
import { TransformerSettingsLayout } from '../transformer-settings-layout'

// TODO: Replace static input with an async Select that loads available FieldCollection types
// via the Pimcore API (GET /api/pimcore-studio/v1/classes/field-collection-definitions or similar).
// Field mappings should be rendered dynamically based on the selected FieldCollection's fields.

interface FieldCollectionOperatorTransformerConfig {
    fieldCollectionKey?: string
    fieldMappings?: Record<string, string>
}

interface FieldCollectionOperatorTransformerFormProps {
    settings: FieldCollectionOperatorTransformerConfig
    onChange: (settings: FieldCollectionOperatorTransformerConfig) => void
}

export const FieldCollectionOperatorTransformerForm = ({ settings, onChange }: FieldCollectionOperatorTransformerFormProps): React.JSX.Element => (
    <TransformerSettingsLayout>
        <Form.Item label="Field Collection Key">
            <Input
                onChange={ (e) => { onChange({ ...settings, fieldCollectionKey: e.target.value }) } }
                placeholder="e.g. MyFieldCollection"
                value={ settings.fieldCollectionKey ?? '' }
            />
        </Form.Item>
    </TransformerSettingsLayout>
)
