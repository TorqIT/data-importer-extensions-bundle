import React from 'react'
import { Form, Input } from '@pimcore/studio-ui-bundle/components'
import { TransformerSettingsLayout } from '../transformer-settings-layout'

interface ConstantTransformerConfig {
    constant?: string
}

interface ConstantTransformerFormProps {
    settings: ConstantTransformerConfig
    onChange: (settings: ConstantTransformerConfig) => void
}

export const ConstantTransformerForm = ({ settings, onChange }: ConstantTransformerFormProps): React.JSX.Element => (
    <TransformerSettingsLayout>
        <Form.Item label="Constant Value">
            <Input
                onChange={ (e) => { onChange({ ...settings, constant: e.target.value }) } }
                value={ settings.constant ?? '' }
            />
        </Form.Item>
    </TransformerSettingsLayout>
)
