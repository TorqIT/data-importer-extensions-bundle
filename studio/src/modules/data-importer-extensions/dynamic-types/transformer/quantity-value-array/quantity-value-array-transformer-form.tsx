import React from 'react'
import { Checkbox, Form, Input, Select } from '@pimcore/studio-ui-bundle/components'
import { TransformerSettingsLayout } from '../transformer-settings-layout'

// Overrides core quantityValueArray (no-settings) with unit configuration.
// TODO: staticUnitSelect should be an async Select loaded from the Pimcore units API.

const UNIT_SOURCE_OPTIONS = [
    { value: 'id', label: 'Unit ID' },
    { value: 'abbreviation', label: 'Abbreviation' },
]

interface QuantityValueArrayTransformerConfig {
    staticUnitSelect?: string | null
    unitSourceSelect?: string
    unitNullIfNoValueCheckbox?: boolean
}

interface QuantityValueArrayTransformerFormProps {
    settings: QuantityValueArrayTransformerConfig
    onChange: (settings: QuantityValueArrayTransformerConfig) => void
}

export const QuantityValueArrayTransformerForm = ({ settings, onChange }: QuantityValueArrayTransformerFormProps): React.JSX.Element => {
    const update = (key: string, value: any): void => { onChange({ ...settings, [key]: value }) }

    return (
        <TransformerSettingsLayout>
            <Form.Item
                extra="TODO: replace with async unit picker from Pimcore API"
                label="Static Unit (ID)"
            >
                <Input
                    onChange={ (e) => { update('staticUnitSelect', e.target.value || null) } }
                    placeholder="Leave empty to use source column"
                    value={ settings.staticUnitSelect ?? '' }
                />
            </Form.Item>
            <Form.Item label="Unit Source">
                <Select
                    onChange={ (v) => { update('unitSourceSelect', v) } }
                    options={ UNIT_SOURCE_OPTIONS }
                    value={ settings.unitSourceSelect ?? 'id' }
                />
            </Form.Item>
            <Form.Item label="Null if no value">
                <Checkbox
                    checked={ settings.unitNullIfNoValueCheckbox ?? false }
                    onChange={ (e) => { update('unitNullIfNoValueCheckbox', e.target.checked) } }
                />
            </Form.Item>
        </TransformerSettingsLayout>
    )
}
