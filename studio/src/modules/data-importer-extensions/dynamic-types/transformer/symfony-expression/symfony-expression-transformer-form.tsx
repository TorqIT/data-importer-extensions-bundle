import React from 'react'
import { Input } from 'antd'
import { Form } from '@pimcore/studio-ui-bundle/components'
import { TransformerSettingsLayout } from '../transformer-settings-layout'

interface SymfonyExpressionTransformerConfig {
    expression?: string
}

interface SymfonyExpressionTransformerFormProps {
    settings: SymfonyExpressionTransformerConfig
    onChange: (settings: SymfonyExpressionTransformerConfig) => void
}

export const SymfonyExpressionTransformerForm = ({ settings, onChange }: SymfonyExpressionTransformerFormProps): React.JSX.Element => (
    <TransformerSettingsLayout>
        <Form.Item
            extra="Available variables: attributes[0], attributes[1], … (values from selected source columns)"
            label="Expression"
        >
            <Input.TextArea
                onChange={ (e) => { onChange({ ...settings, expression: e.target.value }) } }
                placeholder="e.g. attributes[0] == 'Demo Value' ? attributes[1] : null"
                rows={ 4 }
                value={ settings.expression ?? '' }
            />
        </Form.Item>
    </TransformerSettingsLayout>
)
