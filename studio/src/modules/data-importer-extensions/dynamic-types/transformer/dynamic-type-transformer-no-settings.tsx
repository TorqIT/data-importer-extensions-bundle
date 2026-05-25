import React from 'react'
import { injectable } from '@pimcore/studio-ui-bundle/app'
import { Text } from '@pimcore/studio-ui-bundle/components'
import { DynamicTypeTransformerAbstract, type TransformerGroup } from './dynamic-type-transformer-abstract'

const NoSettingsContent = (): React.JSX.Element => (
    <Text type="secondary">No additional settings</Text>
)

function makeNoSettings (id: string, label: string, group: TransformerGroup): new () => DynamicTypeTransformerAbstract {
    @injectable()
    class NoSettingsType extends DynamicTypeTransformerAbstract {
        readonly id = id
        readonly label = label
        readonly group = group

        renderSettings (): React.JSX.Element | null {
            return <NoSettingsContent />
        }
    }
    return NoSettingsType
}

// dataManipulation
export const DynamicTypeTransformerEachAsArray = makeNoSettings('eachAsArray', 'Each As Array', 'dataManipulation')
export const DynamicTypeTransformerSafeKey = makeNoSettings('safeKey', 'Safe Key', 'dataManipulation')
export const DynamicTypeTransformerSlugify = makeNoSettings('slugify', 'Slugify', 'dataManipulation')

// dataTypes
export const DynamicTypeTransformerAsCountryCode = makeNoSettings('asCountryCode', 'As Country Code', 'dataTypes')
export const DynamicTypeTransformerAsLink = makeNoSettings('asLink', 'As Link', 'dataTypes')
