import React from 'react'
import { Form, Input } from '@pimcore/studio-ui-bundle/components'
import { TransformerSettingsLayout } from '../transformer-settings-layout'

interface AsTableTransformerConfig {
    columnDelimiter?: string
    rowDelimiter?: string
}

interface AsTableTransformerFormProps {
    settings: AsTableTransformerConfig
    onChange: (settings: AsTableTransformerConfig) => void
}

export const AsTableTransformerForm = ({ settings, onChange }: AsTableTransformerFormProps): React.JSX.Element => {
    const update = (key: string, value: any): void => { onChange({ ...settings, [key]: value }) }

    return (
        <TransformerSettingsLayout>
            <Form.Item label="Column Delimiter">
                <Input
                    onChange={ (e) => { update('columnDelimiter', e.target.value) } }
                    value={ settings.columnDelimiter ?? ',' }
                />
            </Form.Item>
            <Form.Item label="Row Delimiter">
                <Input
                    onChange={ (e) => { update('rowDelimiter', e.target.value) } }
                    value={ settings.rowDelimiter ?? '|' }
                />
            </Form.Item>
        </TransformerSettingsLayout>
    )
}
