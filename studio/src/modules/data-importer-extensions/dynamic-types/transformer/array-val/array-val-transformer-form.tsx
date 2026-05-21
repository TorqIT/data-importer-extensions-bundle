import React from 'react'
import { Checkbox, Form, Input } from '@pimcore/studio-ui-bundle/components'
import { TransformerSettingsLayout } from '../transformer-settings-layout'

interface ArrayValTransformerConfig {
    index?: string | number
    recursiveSearch?: boolean
    returnNullIfNotFound?: boolean
}

interface ArrayValTransformerFormProps {
    settings: ArrayValTransformerConfig
    onChange: (settings: ArrayValTransformerConfig) => void
}

export const ArrayValTransformerForm = ({ settings, onChange }: ArrayValTransformerFormProps): React.JSX.Element => {
    const update = (key: string, value: any): void => { onChange({ ...settings, [key]: value }) }

    return (
        <TransformerSettingsLayout>
            <Form.Item label="Array Value (index or key)">
                <Input
                    onChange={ (e) => { update('index', e.target.value) } }
                    value={ String(settings.index ?? 0) }
                />
            </Form.Item>
            <Form.Item label="Search Arrays (recursive)">
                <Checkbox
                    checked={ settings.recursiveSearch ?? false }
                    onChange={ (e) => { update('recursiveSearch', e.target.checked) } }
                />
            </Form.Item>
            <Form.Item label="Return null if key nonexistent">
                <Checkbox
                    checked={ settings.returnNullIfNotFound ?? false }
                    onChange={ (e) => { update('returnNullIfNotFound', e.target.checked) } }
                />
            </Form.Item>
        </TransformerSettingsLayout>
    )
}
