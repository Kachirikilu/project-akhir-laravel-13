<td
    class="px-6 py-4 
            {{ (($isBorderX ?? false) || ($isMain ?? false) || ($sortField == $sortFieldString)) ? 'border-l border-r' : '' }}
            {{ ($isBorderL ?? false) ? 'border-l' : '' }}
            {{ ($isBorderR ?? false) ? 'border-r' : '' }}

            @if ($sortField == $sortFieldString) 
                {{ ($isMain ?? false) ? 'border-blue-400 bg-blue-200/30 font-medium' : 'border-blue-300 bg-blue-100/20' }} 
            @else 
                {{ ($isMain ?? false) ? 'border-gray-300 bg-gray-100/30 font-medium' : 'bg-gray-50/10' }}
            @endif
            
            {{ ($isCenter ?? false) ? 'text-center' : '' }}
            text-sm text-gray-700">
    {{ $xData ?? null }}
@if ($isOnlyHeadTd ?? false)
@else
</td>
@endif